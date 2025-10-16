<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Venta;
use App\Models\Inventario;
use App\Models\DetalleVenta;
use App\Models\Promocion;
use App\Models\VentaPromocion;
use App\Models\RechazoTemporal;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ProductoNivelPrecio;

class VentaController extends Controller
{
    /**
     * Regresa el precio del cliente para un producto (por nivel) o el base si no hay
     */
    private function precioCliente(int $productoId, ?int $nivelId): float
    {
        if ($nivelId) {
            $pp = ProductoNivelPrecio::where('producto_id', $productoId)
                ->where('nivel_precio_id', $nivelId)
                ->first();
            if ($pp && $pp->precio !== null) {
                return (float) $pp->precio;
            }
        }
        return (float) (Producto::find($productoId)?->precio ?? 0);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'observaciones' => 'nullable|string',

            // Productos sueltos
            'productos' => 'nullable|array',
            'productos.*.producto_id' => 'required_with:productos|exists:productos,id',
            'productos.*.cantidad' => 'required_with:productos|integer|min:1',
            // ⚠️ Lo ignoramos; el backend recalcula por nivel:
            'productos.*.precio_unitario' => 'nullable|numeric|min:0',

            // Promociones
            'promociones' => 'nullable|array',
            'promociones.*.promocion_id' => 'required_with:promociones|exists:promociones,id',
            'promociones.*.cantidad' => 'required_with:promociones|integer|min:1',

            // Opción B: solo IDs de rechazos para esta venta
            'rechazos_ids' => 'nullable|array',
            'rechazos_ids.*' => 'integer|exists:rechazos_temporales,id',
        ]);

        $vendedor  = $request->user();
        $almacenId = optional($vendedor->almacen)->id ?? ($vendedor->almacen_id ?? 1);

        // Nivel de precio del cliente
        $cliente = Cliente::findOrFail($request->cliente_id);
        $nivelId = $cliente->nivel_precio_id;

        try {
            $result = DB::transaction(function () use ($request, $vendedor, $almacenId, $nivelId) {

                $total = 0.0;

                // 1) Validar promociones y stock, y sumar total de promos
                foreach ($request->promociones ?? [] as $promoData) {
                    $promo = Promocion::with('productos')->find($promoData['promocion_id']);
                    $veces = (int) $promoData['cantidad'];

                    if (!$promo || !$promo->activo) {
                        abort(422, "Promoción inválida.");
                    }

                    foreach ($promo->productos as $producto) {
                        $necesito = (int)($producto->pivot->cantidad ?? 1) * $veces;

                        $stock = Inventario::where('almacen_id', $almacenId)
                            ->where('producto_id', $producto->id)
                            ->sum('cantidad');

                        if ($stock < $necesito) {
                            abort(422, "Stock insuficiente para '{$producto->nombre}' en la promoción.");
                        }
                    }

                    // Las promos se cobran a su precio de promoción (no por nivel)
                    $total += ((float)$promo->precio) * $veces;
                }

                // 2) Validar productos sueltos y sumar total con precio por nivel
                foreach ($request->productos ?? [] as $item) {
                    $stockTotal = Inventario::where('almacen_id', $almacenId)
                        ->where('producto_id', $item['producto_id'])
                        ->sum('cantidad');

                    if ($stockTotal < (int)$item['cantidad']) {
                        abort(422, "Stock insuficiente para el producto ID {$item['producto_id']}");
                    }

                    $precioUnit = $this->precioCliente((int)$item['producto_id'], $nivelId);
                    $total += ((int)$item['cantidad']) * $precioUnit;
                }

                // 3) Crear venta
                $venta = Venta::create([
                    'cliente_id'    => $request->cliente_id,
                    'vendedor_id'   => $vendedor->id,
                    'fecha'         => now(),
                    'total'         => $total,
                    'observaciones' => $request->observaciones,
                ]);

                // 4) Descontar productos sueltos (FIFO por caducidad) y crear detalle_ventas
                foreach ($request->productos ?? [] as $item) {
                    $cantidadRestante = (int)$item['cantidad'];
                    $precioUnit       = $this->precioCliente((int)$item['producto_id'], $nivelId);

                    $lotes = Inventario::where('almacen_id', $almacenId)
                        ->where('producto_id', $item['producto_id'])
                        ->where('cantidad', '>', 0)
                        ->orderBy('fecha_caducidad', 'asc')
                        ->get();

                    foreach ($lotes as $lote) {
                        if ($cantidadRestante <= 0) break;

                        $descontar = min((int)$lote->cantidad, $cantidadRestante);
                        $lote->decrement('cantidad', $descontar);

                        DetalleVenta::create([
                            'venta_id'        => $venta->id,
                            'producto_id'     => $item['producto_id'],
                            'cantidad'        => $descontar,
                            'precio_unitario' => $precioUnit, // 👈 precio por nivel
                            'subtotal'        => $descontar * $precioUnit,
                            'almacen_id'      => $almacenId,
                            'lote'            => $lote->lote,
                            'fecha_caducidad' => $lote->fecha_caducidad,
                        ]);

                        $cantidadRestante -= $descontar;
                    }
                }

                // 5) Registrar promociones vendidas + descontar inventario incluido
                foreach ($request->promociones ?? [] as $promoData) {
                    $promo = Promocion::with('productos')->find($promoData['promocion_id']);
                    $veces = (int)$promoData['cantidad'];

                    // registra venta_promociones
                    VentaPromocion::create([
                        'venta_id'         => $venta->id,
                        'promocion_id'     => $promo->id,
                        'cantidad'         => $veces,
                        'precio_promocion' => (float)$promo->precio,
                    ]);

                    // descuenta inventario de cada producto incluido (precio 0 en detalle, rastro)
                    foreach ($promo->productos as $producto) {
                        $cantidadTotal    = (int)($producto->pivot->cantidad ?? 1) * $veces;
                        $cantidadRestante = $cantidadTotal;

                        $lotes = Inventario::where('almacen_id', $almacenId)
                            ->where('producto_id', $producto->id)
                            ->where('cantidad', '>', 0)
                            ->orderBy('fecha_caducidad', 'asc')
                            ->get();

                        foreach ($lotes as $lote) {
                            if ($cantidadRestante <= 0) break;

                            $descontar = min((int)$lote->cantidad, $cantidadRestante);
                            $lote->decrement('cantidad', $descontar);

                            DetalleVenta::create([
                                'venta_id'        => $venta->id,
                                'producto_id'     => $producto->id,
                                'cantidad'        => $descontar,
                                'precio_unitario' => 0,
                                'subtotal'        => 0,
                                'almacen_id'      => $almacenId,
                                'lote'            => $lote->lote,
                                'fecha_caducidad' => $lote->fecha_caducidad,
                                // si agregaste columna promocion_id en detalle_ventas, puedes setearla
                                // 'promocion_id'  => $promo->id,
                            ]);

                            $cantidadRestante -= $descontar;
                        }
                    }
                }

                // 6) Opción B: ligar SOLO los rechazos enviados por ID y mover a almacén 3
                $ids = $request->input('rechazos_ids', []);
                if (!empty($ids)) {
                    RechazoTemporal::whereIn('id', $ids)
                        ->where('vendedor_id', $vendedor->id)
                        ->whereNull('venta_id')
                        ->update(['venta_id' => $venta->id, 'almacen_id' => 3]);

                    $rechazos = RechazoTemporal::whereIn('id', $ids)->get();

                    foreach ($rechazos as $rechazo) {
                        $inv = Inventario::firstOrNew([
                            'producto_id' => $rechazo->producto_id,
                            'almacen_id'  => 3,
                            'lote'        => $rechazo->lote,
                        ]);
                        $inv->cantidad = (float)($inv->cantidad ?? 0) + (float)$rechazo->cantidad;
                        $inv->fecha_caducidad = $rechazo->fecha_caducidad ?? $inv->fecha_caducidad;
                        $inv->save();
                    }
                }

                return [
                    'venta_id' => $venta->id,
                    'total'    => $total,
                ];
            });

            return response()->json([
                'message'  => 'Venta registrada correctamente.',
                'venta_id' => $result['venta_id'],
                'total'    => $result['total'],
            ], 201);

        } catch (\Throwable $e) {
            \Log::error('Venta store failed', ['err' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error al registrar la venta.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
