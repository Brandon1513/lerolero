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
        'productos' => 'required|array',
        'productos.*' => 'nullable|integer|min:1',
        'observaciones' => 'nullable|string|max:500',
    ]);

    // Validar existencia de stock antes de continuar
    foreach ($request->productos as $productoId => $cantidad) {
        if (!$cantidad || $cantidad < 1) continue;

        $inventarioOrigen = Inventario::where('almacen_id', $request->almacen_origen_id)
            ->where('producto_id', $productoId)
            ->first();

        if (!$inventarioOrigen || $inventarioOrigen->cantidad < $cantidad) {
            return back()->withErrors([
                "productos.$productoId" => "No hay suficiente stock para el producto seleccionado en el almacén origen.",
            ])->withInput();
        }
    }

    DB::transaction(function () use ($request) {
        // 1. Crear el traslado
        $traslado = Traslado::create([
            'almacen_origen_id' => $request->almacen_origen_id,
            'almacen_destino_id' => $request->almacen_destino_id,
            'fecha' => $request->fecha,
            'observaciones' => $request->observaciones,
        ]);

        // 2. Recorrer productos
        foreach ($request->productos as $productoId => $cantidad) {
            if (!$cantidad || $cantidad < 1) continue;

            // 2.1 Crear el detalle del traslado
            DetalleTraslado::create([
                'traslado_id' => $traslado->id,
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
            ]);

            // 2.2 Actualizar inventario - Restar del almacén origen
            Inventario::where('almacen_id', $request->almacen_origen_id)
                ->where('producto_id', $productoId)
                ->decrement('cantidad', $cantidad);

            // 2.3 Actualizar inventario - Sumar al almacén destino
            $invDestino = Inventario::firstOrNew([
                'almacen_id' => $request->almacen_destino_id,
                'producto_id' => $productoId,
            ]);
            $invDestino->cantidad = ($invDestino->cantidad ?? 0) + $cantidad;
            $invDestino->save();
        }
    });
    $almacenDestino = Almacen::find($request->almacen_destino_id);

        if ($almacenDestino && $almacenDestino->tipo === 'vendedor') {
            $productosIniciales = [];

            foreach ($request->productos as $productoId => $cantidad) {
                if ($cantidad && $cantidad > 0) {
                    $producto = Producto::find($productoId);
                    if ($producto) {
                        $productosIniciales[] = [
                            'producto_id' => $productoId,
                            'nombre' => $producto->nombre,
                            'cantidad' => $cantidad,
                        ];
                    }
                }
            }

            // Buscar cierre pendiente para ese vendedor y fecha
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
}
