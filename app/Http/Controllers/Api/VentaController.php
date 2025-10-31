<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Venta;
use App\Models\Inventario;
use App\Models\DetalleVenta;
use App\Models\Promocion;
use App\Models\VentaPromocion;
use App\Models\RechazoTemporal;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\ProductoNivelPrecio;
use App\Models\PagoVenta;

class VentaController extends Controller
{
    /** para pasar avisos cuando la política es 'warn' */
    protected ?string $creditWarning = null;

    /** Precio por nivel para el cliente (o precio base) */
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

    /** Crear una venta (contado, parcial o crédito) */
    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'    => 'required|exists:clientes,id',
            'observaciones' => 'nullable|string',

            // Productos sueltos
            'productos'                   => 'nullable|array',
            'productos.*.producto_id'     => 'required_with:productos|exists:productos,id',
            'productos.*.cantidad'        => 'required_with:productos|integer|min:1',
            'productos.*.precio_unitario' => 'nullable|numeric|min:0', // se recalcula en back

            // Promociones
            'promociones'                   => 'nullable|array',
            'promociones.*.promocion_id'    => 'required_with:promociones|exists:promociones,id',
            'promociones.*.cantidad'        => 'required_with:promociones|integer|min:1',

            // Rechazos (opcional)
            'rechazos_ids'   => 'nullable|array',
            'rechazos_ids.*' => 'integer|exists:rechazos_temporales,id',

            // Pagos y crédito
            'pagos'                 => 'nullable|array',
            'pagos.*.metodo'        => 'required_with:pagos|in:efectivo,transferencia,tarjeta',
            'pagos.*.monto'         => 'required_with:pagos|numeric|min:0',
            'pagos.*.referencia'    => 'nullable|string|max:191',
            'es_credito'            => 'nullable|boolean',
            'fecha_vencimiento'     => 'nullable|date',
        ]);

        $vendedor  = $request->user();
        $almacenId = optional($vendedor->almacen)->id ?? ($vendedor->almacen_id ?? 1);

        $cliente = Cliente::findOrFail($request->cliente_id);
        $nivelId = $cliente->nivel_precio_id;

        /** ---------- Política de crédito ---------- */
        $policy = config('ventas.credit_policy', 'strict'); // strict|warn
        $limit  = (float) config('ventas.credit_limit', 0); // 0 = sin límite

        $saldoPendienteCliente = Venta::where('cliente_id', $cliente->id)
            ->whereIn('estado', ['credito', 'parcial'])
            ->sum('saldo_pendiente');

        $existeCreditoPendiente = $saldoPendienteCliente > 0;

        if ($policy === 'strict' && $existeCreditoPendiente) {
            abort(422, "El cliente tiene saldo pendiente ($" . number_format($saldoPendienteCliente, 2) . "). Debe liquidar o abonar antes de una nueva venta.");
        }

        if ($limit > 0 && $saldoPendienteCliente > $limit) {
            if ($policy === 'strict') {
                abort(422, "Saldo pendiente $" . number_format($saldoPendienteCliente, 2) .
                    " supera el límite de crédito $" . number_format($limit, 2) . ".");
            }
            $this->creditWarning = "Saldo pendiente $" . number_format($saldoPendienteCliente, 2) .
                " supera el límite de crédito $" . number_format($limit, 2) . ".";
        } elseif ($policy === 'warn' && $existeCreditoPendiente) {
            $this->creditWarning = "El cliente tiene saldo pendiente: $" . number_format($saldoPendienteCliente, 2) . ".";
        }
        /** ---------------------------------------- */

        try {
            $result = DB::transaction(function () use ($request, $vendedor, $almacenId, $nivelId) {

                $total = 0.0;

                // 1) Promos: validar stock + acumular total
                foreach ($request->promociones ?? [] as $promoData) {
                    $promo = Promocion::with('productos')->find($promoData['promocion_id']);
                    $veces = (int) $promoData['cantidad'];

                    if (!$promo || !$promo->activo) abort(422, "Promoción inválida.");

                    foreach ($promo->productos as $producto) {
                        $necesito = (int)($producto->pivot->cantidad ?? 1) * $veces;
                        $stock = Inventario::where('almacen_id', $almacenId)
                            ->where('producto_id', $producto->id)
                            ->sum('cantidad');
                        if ($stock < $necesito) {
                            abort(422, "Stock insuficiente para '{$producto->nombre}' en la promoción.");
                        }
                    }

                    $total += ((float) $promo->precio) * $veces;
                }

                // 2) Productos sueltos: validar stock + total con precio por nivel
                foreach ($request->productos ?? [] as $item) {
                    $stockTotal = Inventario::where('almacen_id', $almacenId)
                        ->where('producto_id', $item['producto_id'])
                        ->sum('cantidad');

                    if ($stockTotal < (int)$item['cantidad']) {
                        abort(422, "Stock insuficiente para el producto ID {$item['producto_id']}.");
                    }

                    $precioUnit = $this->precioCliente((int)$item['producto_id'], $nivelId);
                    $total += ((int)$item['cantidad']) * $precioUnit;
                }

                // 3) Validar pagos vs total y crédito
                $pagos       = collect($request->pagos ?? [])->filter(fn($p) => ($p['monto'] ?? 0) > 0);
                $sumaPagos   = (float) $pagos->sum('monto');
                $esCredito   = (bool) $request->boolean('es_credito');
                $fv          = $request->filled('fecha_vencimiento')
                                ? Carbon::parse($request->fecha_vencimiento)
                                : null;

                // - Si NO es crédito: pagos ≈ total (tolerancia 0.5)
                // - Si ES crédito: pagos <= total
                $eps = 0.5;
                if (!$esCredito) {
                    if (abs($sumaPagos - $total) > $eps) {
                        abort(422, "Los pagos deben cubrir el total de la venta. Total: $total, Pagos: $sumaPagos");
                    }
                } else {
                    if ($sumaPagos - $total > $eps) {
                        abort(422, "La suma de pagos no puede exceder el total en venta a crédito.");
                    }
                }

                $saldoPendiente = max($total - $sumaPagos, 0);
                $estado = $esCredito
                    ? ($saldoPendiente > 0 ? 'credito' : 'pagada')
                    : (abs($saldoPendiente) <= $eps ? 'pagada' : 'parcial');

                // 4) Crear venta
                $venta = Venta::create([
                    'cliente_id'        => $request->cliente_id,
                    'vendedor_id'       => $vendedor->id,
                    'fecha'             => now(),
                    'total'             => $total,
                    'observaciones'     => $request->observaciones,

                    'es_credito'        => $esCredito,
                    'total_pagado'      => $sumaPagos,
                    'saldo_pendiente'   => $saldoPendiente,
                    'fecha_vencimiento' => $fv,
                    'estado'            => $estado,
                ]);

                // 5) Descontar productos sueltos (FIFO) + detalle_ventas
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
                            'precio_unitario' => $precioUnit,
                            'subtotal'        => $descontar * $precioUnit,
                            'almacen_id'      => $almacenId,
                            'lote'            => $lote->lote,
                            'fecha_caducidad' => $lote->fecha_caducidad,
                        ]);

                        $cantidadRestante -= $descontar;
                    }
                }

                // 6) Registrar promociones vendidas + descontar inventario incluido
                foreach ($request->promociones ?? [] as $promoData) {
                    $promo = Promocion::with('productos')->find($promoData['promocion_id']);
                    $veces = (int)$promoData['cantidad'];

                    VentaPromocion::create([
                        'venta_id'         => $venta->id,
                        'promocion_id'     => $promo->id,
                        'cantidad'         => $veces,
                        'precio_promocion' => (float)$promo->precio,
                    ]);

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
                            ]);

                            $cantidadRestante -= $descontar;
                        }
                    }
                }

                // 7) Ligar rechazos (opcional)
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

                // 8) Guardar pagos recibidos
                foreach ($pagos as $pago) {
                    PagoVenta::create([
                        'venta_id'    => $venta->id,
                        'metodo'      => $pago['metodo'],
                        'monto'       => (float) $pago['monto'],
                        'referencia'  => $pago['referencia'] ?? null,
                        'cobrador_id' => $vendedor->id, // 👈 vendedor que cobró
                        'fecha'       => now(),
                    ]);
                }

                return [
                    'venta_id'        => $venta->id,
                    'total'           => $total,
                    'suma_pagos'      => $sumaPagos,
                    'saldo_pendiente' => $saldoPendiente,
                    'estado'          => $estado,
                ];
            });

            return response()->json([
                'message'         => 'Venta registrada correctamente.',
                'venta_id'        => $result['venta_id'],
                'total'           => $result['total'],
                'pagado'          => $result['suma_pagos'],
                'saldo_pendiente' => $result['saldo_pendiente'],
                'estado'          => $result['estado'],
                'warning'         => $this->creditWarning, // null si no aplica
            ], 201);

        } catch (\Throwable $e) {
            \Log::error('Venta store failed', ['err' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'message' => 'Error al registrar la venta.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /** Registrar un abono a una venta existente */
    public function abonar(Request $request, Venta $venta)
    {
        $request->validate([
            'metodo'     => 'required|in:efectivo,transferencia,tarjeta',
            'monto'      => 'required|numeric|min:0.01',
            'referencia' => 'nullable|string|max:191',
        ]);

        if (!in_array($venta->estado, ['credito', 'parcial'])) {
            return response()->json([
                'message' => 'Esta venta no admite abonos (ya está pagada o cancelada).',
            ], 422);
        }

        $vendedor = $request->user();

        return DB::transaction(function () use ($request, $venta, $vendedor) {

            PagoVenta::create([
                'venta_id'    => $venta->id,
                'metodo'      => $request->metodo,
                'monto'       => (float) $request->monto,
                'referencia'  => $request->referencia,
                'cobrador_id' => $vendedor->id, // 👈 quién cobra
                'fecha'       => now(),
            ]);

            $totalPagado = (float) $venta->pagos()->sum('monto');
            $saldo       = max(0, (float) $venta->total - $totalPagado);

            $venta->update([
                'total_pagado'    => $totalPagado,
                'saldo_pendiente' => $saldo,
                'estado'          => $saldo <= 0.01 ? 'pagada' : 'parcial',
            ]);

            return response()->json([
                'message'         => 'Abono registrado.',
                'venta_id'        => $venta->id,
                'total'           => $venta->total,
                'total_pagado'    => $venta->total_pagado,
                'saldo_pendiente' => $venta->saldo_pendiente,
                'estado'          => $venta->estado,
            ], 201);
        });
    }
}
