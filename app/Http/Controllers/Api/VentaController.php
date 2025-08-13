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
    // 👇 Toma el almacén de la relación o del campo directo; cae a 1 si no hay
    $almacenId = optional($vendedor->almacen)->id ?? ($vendedor->almacen_id ?? 1);

    DB::beginTransaction();

    try {
        $total = 0;

        // 1) Validar promociones y stock
        foreach ($request->promociones ?? [] as $promoData) {
            $promo = Promocion::with('productos')->find($promoData['promocion_id']);
            $veces = $promoData['cantidad'];

            if (!$promo || !$promo->activo) {
                return response()->json(['message' => "Promoción inválida."], 422);
            }

            foreach ($promo->productos as $producto) {
                $cantidad = ($producto->pivot->cantidad ?? 1) * $veces;

                $stock = Inventario::where('almacen_id', $almacenId)
                    ->where('producto_id', $producto->id)
                    ->sum('cantidad');

                if ($stock < $cantidad) {
                    return response()->json([
                        'message' => "Stock insuficiente para producto '{$producto->nombre}' en la promoción."
                    ], 422);
                }
            }

            $total += $promo->precio * $veces;
        }

        // 2) Validar productos individuales
        foreach ($request->productos ?? [] as $item) {
            $stockTotal = Inventario::where('almacen_id', $almacenId)
                ->where('producto_id', $item['producto_id'])
                ->sum('cantidad');

            if ($stockTotal < $item['cantidad']) {
                return response()->json([
                    'message' => "Stock insuficiente para el producto ID {$item['producto_id']}"
                ], 422);
            }

            $total += $item['cantidad'] * $item['precio_unitario'];
        }

        // 3) Crear venta
        $venta = Venta::create([
            'cliente_id'    => $request->cliente_id,
            'vendedor_id'   => $vendedor->id,   // ✅ tu modelo usa vendedor_id
            'fecha'         => now(),
            'total'         => $total,
            'observaciones' => $request->observaciones,
        ]);

        // 4) Procesar productos individuales (descuento FIFO por caducidad)
        foreach ($request->productos ?? [] as $item) {
            $cantidadRestante = (int)$item['cantidad'];

            $lotes = Inventario::where('almacen_id', $almacenId)
                ->where('producto_id', $item['producto_id'])
                ->where('cantidad', '>', 0)
                ->orderBy('fecha_caducidad', 'asc')
                ->get();

            foreach ($lotes as $lote) {
                if ($cantidadRestante <= 0) break;

                $descontar = min($lote->cantidad, $cantidadRestante);
                $lote->decrement('cantidad', $descontar);

                DetalleVenta::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $descontar,
                    'precio_unitario' => $item['precio_unitario'],
                    'subtotal'        => $descontar * $item['precio_unitario'],
                    'almacen_id'      => $almacenId,
                    'lote'            => $lote->lote,
                    'fecha_caducidad' => $lote->fecha_caducidad,
                    // 'es_promocion'  => false, // 👈 si tu tabla lo tiene, déjalo en false
                    // 'promocion_id'  => null,
                ]);

                $cantidadRestante -= $descontar;
            }
        }

        // 5) Procesar promociones (descontar inventario de lo incluido + registrar venta_promociones)
        foreach ($request->promociones ?? [] as $promoData) {
            $promo = Promocion::with('productos')->find($promoData['promocion_id']);
            $veces = (int)$promoData['cantidad'];

            // 👇 REGISTRO en venta_promociones (para tu bloque "Promociones vendidas")
            \App\Models\VentaPromocion::create([
                'venta_id'         => $venta->id,
                'promocion_id'     => $promo->id,
                'cantidad'         => $veces,
                'precio_promocion' => $promo->precio,
            ]);

            foreach ($promo->productos as $producto) {
                $cantidadTotal = (int)($producto->pivot->cantidad ?? 1) * $veces;
                $cantidadRestante = $cantidadTotal;

                $lotes = Inventario::where('almacen_id', $almacenId)
                    ->where('producto_id', $producto->id)
                    ->where('cantidad', '>', 0)
                    ->orderBy('fecha_caducidad', 'asc')
                    ->get();

                foreach ($lotes as $lote) {
                    if ($cantidadRestante <= 0) break;

                    $descontar = min($lote->cantidad, $cantidadRestante);
                    $lote->decrement('cantidad', $descontar);

                    // Línea "fantasma" opcional (rastro de inventario), precio 0:
                    DetalleVenta::create([
                        'venta_id'        => $venta->id,
                        'producto_id'     => $producto->id,
                        'cantidad'        => $descontar,
                        'precio_unitario' => 0,
                        'subtotal'        => 0,
                        'almacen_id'      => $almacenId,
                        'lote'            => $lote->lote,
                        'fecha_caducidad' => $lote->fecha_caducidad,
                        // 'promocion_id'  => $promo->id, // 👈 si añadiste esta col. en detalle_ventas
                    ]);

                    $cantidadRestante -= $descontar;
                }
            }
        }

        // 6) Procesar rechazos (igual que tu versión)
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
                'almacen_id'  => 3
            ]);

            $inventario->cantidad = ($inventario->cantidad ?? 0) + $rechazo->cantidad;
            $inventario->lote = $rechazo->lote;
            $inventario->fecha_caducidad = $rechazo->fecha_caducidad;
            $inventario->save();
        }

        DB::commit();

        return response()->json([
            'message'  => 'Venta registrada correctamente.',
            'venta_id' => $venta->id,
        ], 201);

    } catch (\Throwable $e) {
        DB::rollBack();
        \Log::error('Venta store failed', ['err' => $e->getMessage()]);
        return response()->json([
            'message' => 'Error al registrar la venta.',
            'error'   => $e->getMessage()
        ], 500);
    }
}

}
