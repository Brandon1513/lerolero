<?php

namespace App\Http\Controllers;

use id;
use App\Models\Producto;
use App\Models\Inventario;
use App\Models\Produccion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LoteInventario;


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

    $produccion = Produccion::create([
        'producto_id' => $request->producto_id,
        'cantidad' => $request->cantidad,
        'fecha' => $request->fecha,
        'lote' => $request->lote,
        'notas' => $request->notas,
        'usuario_id' => auth()->id(),
    ]);

    // 1. Guardar en lotes_inventario
    LoteInventario::create([
        'producto_id' => $request->producto_id,
        'almacen_id' => 1,
        'lote' => $request->lote,
        'fecha_caducidad' => $request->fecha_caducidad,
        'cantidad' => $request->cantidad,
    ]);

    // 2. Actualizar o crear en inventario_almacen
    Inventario::updateOrCreate(
        [
            'producto_id' => $request->producto_id,
            'almacen_id' => 1,
            'lote' => $request->lote,
            'fecha_caducidad' => $request->fecha_caducidad,
        ],
        [
            'cantidad' => DB::raw("cantidad + {$request->cantidad}")
        ]
    );

    return redirect()->route('producciones.index')->with('success', 'Producción registrada con lote y caducidad.');
}


public function destroy(Produccion $produccion)
{
    DB::transaction(function () use ($produccion) {
        // 1. Revertir en inventario_almacen (almacén general = ID 1)
        $inventario = \App\Models\Inventario::where('producto_id', $produccion->producto_id)
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

        // 2. Revertir en lotes_inventario
        $lote = \App\Models\LoteInventario::where('producto_id', $produccion->producto_id)
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

        // 3. Eliminar la producción
        $produccion->delete();
    });

    return redirect()->route('producciones.index')->with('success', 'Producción eliminada y stock revertido.');
}


}
