<?php

namespace App\Http\Controllers\Api;

use App\Models\Venta;
use App\Models\Inventario;
use App\Models\DetalleVenta;
use App\Models\Promocion;
use App\Models\RechazoTemporal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VentaController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'promociones' => 'nullable|array',
            'promociones.*.promocion_id' => 'required|exists:promociones,id',
            'promociones.*.cantidad' => 'required|integer|min:1',
            'observaciones' => 'nullable|string',
        ]);

        $vendedor = $request->user();

        DB::beginTransaction();

        try {
            $total = 0;

            // Validar promociones y stock
            foreach ($request->promociones ?? [] as $promoData) {
                $promo = Promocion::with('productos')->find($promoData['promocion_id']);
                $veces = $promoData['cantidad'];

                if (!$promo || !$promo->activo) {
                    return response()->json(['message' => "Promoción inválida."], 422);
                }

                foreach ($promo->productos as $producto) {
                    $cantidad = $producto->pivot->cantidad * $veces;

                    $stock = Inventario::where('almacen_id', $vendedor->almacen->id)
                        ->where('producto_id', $producto->id)
                        ->sum('cantidad');

                    if ($stock < $cantidad) {
                        return response()->json(['message' => "Stock insuficiente para producto '{$producto->nombre}' en la promoción."], 422);
                    }
                }

                $total += $promo->precio * $veces;
            }

            // Validar productos individuales
            foreach ($request->productos ?? [] as $item) {
                $stockTotal = Inventario::where('almacen_id', $vendedor->almacen->id)
                    ->where('producto_id', $item['producto_id'])
                    ->sum('cantidad');

                if ($stockTotal < $item['cantidad']) {
                    return response()->json(['message' => "Stock insuficiente para el producto ID {$item['producto_id']}"], 422);
                }

                $total += $item['cantidad'] * $item['precio_unitario'];
            }

            // Crear venta
            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'vendedor_id' => $vendedor->id,
                'fecha' => now(),
                'total' => $total,
                'observaciones' => $request->observaciones,
            ]);

            // Procesar productos individuales
            foreach ($request->productos ?? [] as $item) {
                $cantidadRestante = $item['cantidad'];

                $lotes = Inventario::where('almacen_id', $vendedor->almacen->id)
                    ->where('producto_id', $item['producto_id'])
                    ->where('cantidad', '>', 0)
                    ->orderBy('fecha_caducidad', 'asc')
                    ->get();

                foreach ($lotes as $lote) {
                    if ($cantidadRestante <= 0) break;

                    $descontar = min($lote->cantidad, $cantidadRestante);
                    $lote->decrement('cantidad', $descontar);

                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $item['producto_id'],
                        'cantidad' => $descontar,
                        'precio_unitario' => $item['precio_unitario'],
                        'subtotal' => $descontar * $item['precio_unitario'],
                        'almacen_id' => $vendedor->almacen->id,
                        'lote' => $lote->lote,
                        'fecha_caducidad' => $lote->fecha_caducidad,
                        'es_promocion' => true,
                    ]);

                    $cantidadRestante -= $descontar;
                }
            }

            // Procesar promociones
            foreach ($request->promociones ?? [] as $promoData) {
                $promo = Promocion::with('productos')->find($promoData['promocion_id']);
                $veces = $promoData['cantidad'];

                foreach ($promo->productos as $producto) {
                    $cantidadTotal = $producto->pivot->cantidad * $veces;
                    $cantidadRestante = $cantidadTotal;

                    $lotes = Inventario::where('almacen_id', $vendedor->almacen->id)
                        ->where('producto_id', $producto->id)
                        ->where('cantidad', '>', 0)
                        ->orderBy('fecha_caducidad', 'asc')
                        ->get();

                    foreach ($lotes as $lote) {
                        if ($cantidadRestante <= 0) break;

                        $descontar = min($lote->cantidad, $cantidadRestante);
                        $lote->decrement('cantidad', $descontar);

                        DetalleVenta::create([
                            'venta_id' => $venta->id,
                            'producto_id' => $producto->id,
                            'cantidad' => $descontar,
                            'precio_unitario' => 0,
                            'subtotal' => 0,
                            'almacen_id' => $vendedor->almacen->id,
                            'lote' => $lote->lote,
                            'fecha_caducidad' => $lote->fecha_caducidad,
                        ]);

                        $cantidadRestante -= $descontar;
                    }
                }
            }

            // Procesar rechazos
            RechazoTemporal::where('vendedor_id', $vendedor->id)
                ->whereNull('venta_id')
                ->update([
                    'venta_id' => $venta->id,
                    'almacen_id' => 3
                ]);

            $rechazos = RechazoTemporal::where('venta_id', $venta->id)->get();

            foreach ($rechazos as $rechazo) {
                $inventario = Inventario::firstOrNew([
                    'producto_id' => $rechazo->producto_id,
                    'almacen_id' => 3
                ]);

                $inventario->cantidad = ($inventario->cantidad ?? 0) + $rechazo->cantidad;
                $inventario->lote = $rechazo->lote;
                $inventario->fecha_caducidad = $rechazo->fecha_caducidad;
                $inventario->save();
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
