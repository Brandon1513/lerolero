<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Inventario;
use App\Models\Produccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LoteInventario;
use App\Models\DetalleTraslado;

class ProduccionController extends Controller
{
    public function index()
    {
        $producciones = Produccion::with('producto', 'usuario')->orderBy('fecha', 'desc')->paginate(10);
        return view('producciones.index', compact('producciones'));
    }
    public function create()
{
    $productos = Producto::orderBy('nombre')->get();
    return view('producciones.create', compact('productos'));
}



public function store(Request $request)
{
    $request->validate([
        'producto_id' => 'required|exists:productos,id',
        'cantidad' => 'required|integer|min:1',
        'fecha' => 'required|date',
        'lote' => 'nullable|string|max:100',
        'fecha_caducidad' => 'required|date|after_or_equal:fecha',
        'notas' => 'nullable|string|max:1000',
    ]);

    DB::transaction(function () use ($request) {

        // 1) Guardar Producción (✅ ahora incluye fecha_caducidad)
        $produccion = Produccion::create([
            'producto_id'      => $request->producto_id,
            'cantidad'         => $request->cantidad,
            'fecha'            => $request->fecha,
            'fecha_caducidad'  => $request->fecha_caducidad, // ✅
            'lote'             => $request->lote,
            'notas'            => $request->notas,
            'usuario_id'       => auth()->id(),
        ]);

        // 2) Guardar en lotes_inventario
        LoteInventario::create([
            'producto_id'     => $request->producto_id,
            'almacen_id'      => 1,
            'lote'            => $request->lote,
            'fecha_caducidad' => $request->fecha_caducidad,
            'cantidad'        => $request->cantidad,
        ]);

        // 3) Actualizar/crear en inventario_almacen (más seguro)
        $inv = Inventario::firstOrCreate(
            [
                'producto_id'     => $request->producto_id,
                'almacen_id'      => 1,
                'lote'            => $request->lote,
                'fecha_caducidad' => $request->fecha_caducidad,
            ],
            [
                'cantidad' => 0,
            ]
        );

        $inv->increment('cantidad', (int)$request->cantidad);
    });

    return redirect()->route('producciones.index')->with('success', 'Producción registrada con lote y caducidad.');
}



public function destroy(Produccion $produccion)
    {
        // ✅ Regla pro: NO borrar si el lote ya salió del almacén general (por traslado)
        $yaSeTrasladoDesdeGeneral = DetalleTraslado::where('producto_id', $produccion->producto_id)
            ->where('lote', $produccion->lote)
            ->where('fecha_caducidad', $produccion->fecha_caducidad)
            ->whereHas('traslado', function ($q) {
                $q->where('almacen_origen_id', 1);
            })
            ->exists();

        if ($yaSeTrasladoDesdeGeneral) {
            return back()->with('error', 'No se puede eliminar: este lote ya fue trasladado desde el Almacén General.');
        }

        // ✅ Regla pro: debe existir completo en almacén general (porque si hubo ventas/ajustes ya no cuadra)
        $cantidadDisponibleGeneral = Inventario::where('producto_id', $produccion->producto_id)
            ->where('almacen_id', 1)
            ->where('lote', $produccion->lote)
            ->where('fecha_caducidad', $produccion->fecha_caducidad)
            ->value('cantidad') ?? 0;

        if ($cantidadDisponibleGeneral < $produccion->cantidad) {
            return back()->with('error', 'No se puede eliminar: el stock en Almacén General ya no coincide (hubo ventas/ajustes/traslados).');
        }

        DB::transaction(function () use ($produccion) {

            // 1) Revertir en inventario_almacen (general=1)
            $inventario = Inventario::where('producto_id', $produccion->producto_id)
                ->where('almacen_id', 1)
                ->where('lote', $produccion->lote)
                ->where('fecha_caducidad', $produccion->fecha_caducidad)
                ->first();

            if ($inventario) {
                if ($inventario->cantidad <= $produccion->cantidad) {
                    $inventario->delete();
                } else {
                    $inventario->decrement('cantidad', $produccion->cantidad);
                }
            }

            // 2) Revertir en lotes_inventario
            $lote = LoteInventario::where('producto_id', $produccion->producto_id)
                ->where('almacen_id', 1)
                ->where('lote', $produccion->lote)
                ->where('fecha_caducidad', $produccion->fecha_caducidad)
                ->first();

            if ($lote) {
                if ($lote->cantidad <= $produccion->cantidad) {
                    $lote->delete();
                } else {
                    $lote->decrement('cantidad', $produccion->cantidad);
                }
            }

            // 3) Eliminar producción
            $produccion->delete();
        });

        return redirect()->route('producciones.index')
            ->with('success', 'Producción eliminada y stock revertido.');
    }



}
