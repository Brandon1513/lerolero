<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Traslado;
use App\Models\Inventario;
use Illuminate\Http\Request;
use App\Models\DetalleTraslado;
use App\Models\InventarioAlmacen;
use Illuminate\Support\Facades\DB;

class TrasladoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $traslados = Traslado::with(['origen', 'destino'])
            ->when($request->filled('fecha_inicio'), function ($query) use ($request) {
                $query->whereDate('fecha', '>=', $request->fecha_inicio);
            })
            ->when($request->filled('fecha_fin'), function ($query) use ($request) {
                $query->whereDate('fecha', '<=', $request->fecha_fin);
            })
            ->when($request->filled('destino_id'), function ($query) use ($request) {
                $query->where('almacen_destino_id', $request->destino_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $almacenes = Almacen::where('activo', true)->get();

        return view('traslados.index', compact('traslados', 'almacenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $almacenes = Almacen::where('activo', true)->get();
        $productos = Producto::where('activo', true)->get();
        $inventario = Inventario::where('almacen_id', 1)->pluck('cantidad', 'producto_id'); // cantidad disponible en el almacén general
        return view('traslados.create', compact('almacenes', 'productos', 'inventario'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([
        'almacen_origen_id' => 'required|exists:almacenes,id|different:almacen_destino_id',
        'almacen_destino_id' => 'required|exists:almacenes,id',
        'fecha' => 'required|date',
        'detalles' => 'required|array',
        'detalles.*.*.cantidad' => 'nullable|numeric|min:1',
        'detalles.*.*.lote' => 'required|string',
        'detalles.*.*.fecha_caducidad' => 'required|date',
    ]);

    try {
        DB::transaction(function () use ($request) {
            $traslado = Traslado::create([
                'almacen_origen_id' => $request->almacen_origen_id,
                'almacen_destino_id' => $request->almacen_destino_id,
                'fecha' => $request->fecha,
                'observaciones' => $request->observaciones,
            ]);

            foreach ($request->detalles as $productoId => $lotes) {
                foreach ($lotes as $detalle) {
                    $cantidad = $detalle['cantidad'];
                    $lote = $detalle['lote'];
                    $caducidad = $detalle['fecha_caducidad'];

                    if (!$cantidad || $cantidad < 1) continue;

                    // Validar stock del lote exacto
                    $loteOrigen = Inventario::where('almacen_id', $request->almacen_origen_id)
                        ->where('producto_id', $productoId)
                        ->where('lote', $lote)
                        ->where('fecha_caducidad', $caducidad)
                        ->first();

                    if (!$loteOrigen || $loteOrigen->cantidad < $cantidad) {
                        throw new \Exception("No hay suficiente stock del lote {$lote} para el producto ID {$productoId}.");
                    }

                    // Registrar detalle del traslado
                    DetalleTraslado::create([
                        'traslado_id' => $traslado->id,
                        'producto_id' => $productoId,
                        'cantidad' => $cantidad,
                        'lote' => $lote,
                        'fecha_caducidad' => $caducidad,
                    ]);

                    // Restar del origen
                    $loteOrigen->decrement('cantidad', $cantidad);

                    // Sumar en destino (mismo lote y fecha)
                    $loteDestino = Inventario::firstOrNew([
                        'almacen_id' => $request->almacen_destino_id,
                        'producto_id' => $productoId,
                        'lote' => $lote,
                        'fecha_caducidad' => $caducidad,
                    ]);
                    $loteDestino->cantidad = ($loteDestino->cantidad ?? 0) + $cantidad;
                    $loteDestino->save();
                }
            }
        });

        // Actualizar inventario inicial en cierre de ruta si aplica
        $almacenDestino = Almacen::find($request->almacen_destino_id);

        if ($almacenDestino && $almacenDestino->tipo === 'vendedor') {
            $productosIniciales = [];

            foreach ($request->detalles as $productoId => $lotes) {
                $total = array_sum(array_column($lotes, 'cantidad'));

                if ($total > 0) {
                    $producto = Producto::find($productoId);
                    if ($producto) {
                        $productosIniciales[] = [
                            'producto_id' => $productoId,
                            'nombre' => $producto->nombre,
                            'cantidad' => $total,
                        ];
                    }
                }
            }

            $cierre = \App\Models\CierreRuta::where('vendedor_id', $almacenDestino->user_id)
                ->whereDate('fecha', $request->fecha)
                ->where('estatus', 'pendiente')
                ->first();

            if ($cierre && !$cierre->inventario_inicial) {
                $cierre->update([
                    'inventario_inicial' => $productosIniciales,
                ]);
            }
        }

        return redirect()->route('traslados.index')->with('success', 'Traslado registrado correctamente.');
    } catch (\Throwable $e) {
        \Log::error('Error en traslado', ['error' => $e->getMessage()]);
        return back()->withErrors(['error' => $e->getMessage()])->withInput();
    }
}

  


    /**
     * Display the specified resource.
     */
    

    public function show(Traslado $traslado)
    {
        $traslado->load(['origen', 'destino', 'detalles.producto']);
        return view('traslados.show', compact('traslado'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function porAlmacen($id)
    {
        $inventario = Inventario::where('almacen_id', $id)
            ->pluck('cantidad', 'producto_id');

        return response()->json($inventario);
    }
    public function lotesPorAlmacen($almacenId)
{
    $lotes = Inventario::with('producto')
        ->where('almacen_id', $almacenId)
        ->where('cantidad', '>', 0)
        ->whereNotNull('lote')
        ->orderBy('producto_id')
        ->orderBy('fecha_caducidad')
        ->get();

    $agrupado = [];

    foreach ($lotes as $lote) {
        $agrupado[$lote->producto_id][] = [
            'lote' => $lote->lote,
            'fecha_caducidad' => $lote->fecha_caducidad,
            'cantidad' => $lote->cantidad,
            'producto' => $lote->producto->nombre ?? 'Producto',
        ];
    }

    return response()->json($agrupado);
}

}
