<?php

namespace App\Http\Controllers\Api;

use App\Models\Venta;
use App\Models\Inventario;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VentaController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'productos' => 'required|array',
        'productos.*.producto_id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|integer|min:1',
        'productos.*.precio_unitario' => 'required|numeric|min:0',
        'observaciones' => 'nullable|string',
    ]);

    $vendedor = $request->user();

    DB::beginTransaction();

    try {
        $total = 0;

        foreach ($request->productos as $item) {
            // 1️⃣ Sumamos stock disponible de lotes
            $stockTotal = \App\Models\Inventario::where('almacen_id', $vendedor->almacen->id)
                ->where('producto_id', $item['producto_id'])
                ->sum('cantidad');

            if ($stockTotal < $item['cantidad']) {
                return response()->json([
                    'message' => "Stock insuficiente para el producto ID {$item['producto_id']}"
                ], 422);
            }

            $total += $item['cantidad'] * $item['precio_unitario'];
        }

        // 2️⃣ Creamos la venta
        $venta = \App\Models\Venta::create([
            'cliente_id' => $request->cliente_id,
            'vendedor_id' => $vendedor->id,
            'fecha' => now(),
            'total' => $total,
            'observaciones' => $request->observaciones,
        ]);

        // 3️⃣ Recorremos y descontamos FIFO
        foreach ($request->productos as $item) {
            $cantidadRestante = $item['cantidad'];

            $lotes = \App\Models\Inventario::where('almacen_id', $vendedor->almacen->id)
                ->where('producto_id', $item['producto_id'])
                ->where('cantidad', '>', 0)
                ->orderBy('fecha_caducidad', 'asc')
                ->get();

            foreach ($lotes as $lote) {
                if ($cantidadRestante <= 0) break;

                $descontar = min($lote->cantidad, $cantidadRestante);

                // Actualizamos lote
                $lote->decrement('cantidad', $descontar);

                // Guardamos detalle de la venta con lote
                \App\Models\DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $descontar,
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal' => $descontar * $item['precio_unitario'],
                    'almacen_id' => $vendedor->almacen->id,
                    'lote' => $lote->lote,
                    'fecha_caducidad' => $lote->fecha_caducidad,
                ]);

                $cantidadRestante -= $descontar;
            }
        }

        DB::commit();

        return response()->json([
            'message' => 'Venta registrada correctamente.',
            'venta_id' => $venta->id,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'message' => 'Error al registrar la venta.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
