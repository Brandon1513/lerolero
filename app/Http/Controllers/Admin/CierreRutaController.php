<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CierreRuta;
use App\Models\User;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Traslado;
use App\Models\DetalleTraslado;
use App\Models\RechazoTemporal;
use App\Models\Almacen;
use Carbon\Carbon;

class CierreRutaController extends Controller
{
    public function index(Request $request)
    {
        $cierres = CierreRuta::with('vendedor', 'cerradoPor')
            ->when($request->vendedor_id, fn($q) => $q->where('vendedor_id', $request->vendedor_id))
            ->when($request->fecha_inicio, fn($q) => $q->whereDate('fecha', '>=', $request->fecha_inicio))
            ->when($request->fecha_fin, fn($q) => $q->whereDate('fecha', '<=', $request->fecha_fin))
            ->when($request->estatus, fn($q) => $q->where('estatus', $request->estatus))
            ->when($request->cerrado_por, fn($q) => $q->where('cerrado_por', $request->cerrado_por))
            ->orderBy('fecha', 'desc')
            ->paginate(10);

        $vendedores = User::role('vendedor')->get();
        $admins = User::role('administrador')->get();

        return view('cierres.index', compact('cierres', 'vendedores', 'admins'));
    }

    public function show(CierreRuta $cierre)
    {
        $cierre->load('vendedor');
        return view('cierres.show', compact('cierre'));
    }

    public function update(Request $request, CierreRuta $cierre)
    {
        $request->validate([
            'total_efectivo' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000',
        ]);
    
        $almacenVendedor = Almacen::where('tipo', 'vendedor')->where('user_id', $cierre->vendedor_id)->first();
        $almacenGeneral = Almacen::where('tipo', 'general')->first();
        $almacenRechazo = Almacen::where('tipo', 'rechazo')->first();
    
        if (!$almacenVendedor || !$almacenGeneral || !$almacenRechazo) {
            return redirect()->route('cierres.index')->withErrors('Error al localizar almacenes.');
        }
    
        // 1. Obtener inventario FINAL antes de vaciarlo
        $inventarioFinal = Inventario::where('almacen_id', $almacenVendedor->id)
            ->get()
            ->map(function ($item) {
                return [
                    'producto_id' => $item->producto_id,
                    'nombre' => optional($item->producto)->nombre,
                    'cantidad' => $item->cantidad,
                ];
            })->toArray();
    
        // 2. Obtener inventario INICIAL desde traslado del día
        $trasladoInicial = Traslado::where('almacen_destino_id', $almacenVendedor->id)
            ->whereDate('fecha', $cierre->fecha)
            ->latest()
            ->first();
    
        $inventarioInicial = [];
    
        if ($trasladoInicial) {
            $inventarioInicial = DetalleTraslado::where('traslado_id', $trasladoInicial->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'producto_id' => $item->producto_id,
                        'nombre' => optional($item->producto)->nombre,
                        'cantidad' => $item->cantidad,
                    ];
                })->toArray();
        }
    
        // 3. Procesar productos rechazados (cambios)
        $rechazos = RechazoTemporal::where('vendedor_id', $cierre->vendedor_id)->get();
        $listaCambios = [];
    
        foreach ($rechazos as $rechazo) {
            $producto = Producto::find($rechazo->producto_id);
            if (!$producto) continue;
    
            $trasladoRechazo = Traslado::create([
                'almacen_origen_id' => $almacenVendedor->id,
                'almacen_destino_id' => $almacenRechazo->id,
                'fecha' => now(),
                'observaciones' => 'Producto rechazado por ' . $rechazo->motivo,
                'user_id' => auth()->id(),
            ]);
    
            DetalleTraslado::create([
                'traslado_id' => $trasladoRechazo->id,
                'producto_id' => $rechazo->producto_id,
                'cantidad' => $rechazo->cantidad,
            ]);
    
            $inventarioRechazo = Inventario::firstOrNew([
                'almacen_id' => $almacenRechazo->id,
                'producto_id' => $rechazo->producto_id,
            ]);
            $inventarioRechazo->cantidad += $rechazo->cantidad;
            $inventarioRechazo->save();
    
            $listaCambios[] = [
                'producto_id' => $rechazo->producto_id,
                'nombre' => $producto->nombre,
                'cantidad' => $rechazo->cantidad,
                'motivo' => $rechazo->motivo,
            ];
    
            $rechazo->delete();
        }
    
        // 4. Crear traslado de productos devueltos al almacén general
        $traslado = Traslado::create([
            'almacen_origen_id' => $almacenVendedor->id,
            'almacen_destino_id' => $almacenGeneral->id,
            'fecha' => now(),
            'observaciones' => 'Devolución por cierre de ruta del vendedor ' . $cierre->vendedor->name,
            'user_id' => auth()->id(),
        ]);
    
        foreach ($inventarioFinal as $item) {
            $producto = Producto::find($item['producto_id']);
            if (!$producto) continue;
    
            DetalleTraslado::create([
                'traslado_id' => $traslado->id,
                'producto_id' => $producto->id,
                'cantidad' => $item['cantidad'],
            ]);
    
            $inventarioGeneral = Inventario::firstOrNew([
                'almacen_id' => $almacenGeneral->id,
                'producto_id' => $producto->id,
            ]);
            $inventarioGeneral->cantidad += $item['cantidad'];
            $inventarioGeneral->save();
        }
    
        // 5. Vaciar inventario del vendedor
        Inventario::where('almacen_id', $almacenVendedor->id)->delete();
    
        // 6. Actualizar cierre
        $cierre->update([
            'total_efectivo' => $request->total_efectivo,
            'observaciones' => $request->observaciones,
            'estatus' => 'cuadrado',
            'cerrado_por' => auth()->id(),
            'inventario_inicial' => $inventarioInicial,
            'inventario_final' => $inventarioFinal,
            'cambios' => $listaCambios,
            'traslado_id' => $traslado->id,
        ]);
    
        $diferencia = $cierre->total_efectivo - $cierre->total_ventas;
        $toast = $diferencia === 0 ? 'cuadrado' : ($diferencia < 0 ? 'faltan' : 'sobran');
    
        return redirect()
            ->route('cierres.index')
            ->with([
                'success' => 'Cierre completado correctamente.',
                'toast' => $toast,
            ]);
    }
    


}
