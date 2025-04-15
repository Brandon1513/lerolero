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
            $inventario = Inventario::where('almacen_id', $vendedor->almacen->id)
                ->where('producto_id', $item['producto_id'])
                ->first();

            if (!$inventario || $inventario->cantidad < $item['cantidad']) {
                return response()->json([
                    'message' => "Stock insuficiente para el producto ID {$item['producto_id']}"
                ], 422);
            }

            $total += $item['cantidad'] * $item['precio_unitario'];
        }

        $venta = Venta::create([
            'cliente_id' => $request->cliente_id,
            'vendedor_id' => $vendedor->id,
            'fecha' => now(),
            'total' => $total,
            'observaciones' => $request->observaciones,
        ]);

        foreach ($request->productos as $item) {
            DetalleVenta::create([
                'venta_id' => $venta->id,
                'producto_id' => $item['producto_id'],
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal' => $item['cantidad'] * $item['precio_unitario'],
            ]);

            // Descontar del inventario del vendedor
            Inventario::where('almacen_id', $vendedor->almacen->id)
                ->where('producto_id', $item['producto_id'])
                ->decrement('cantidad', $item['cantidad']);
        }

        DB::commit();

        return response()->json([
            'message' => 'Venta registrada exitosamente.',
            'venta_id' => $venta->id,
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error al registrar la venta.', 'error' => $e->getMessage()], 500);
    }
}
}
