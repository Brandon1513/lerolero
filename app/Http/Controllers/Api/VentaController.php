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
use App\Models\VisitaCliente;

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
            'productos.*.precio_unitario' => 'nullable|numeric|min:0',

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

            // Idempotencia
            'client_tx_id'          => 'nullable|string|max:64',

            // ✅ NOTA: Las coordenadas GPS se guardan en visitas_clientes, no en ventas
            'latitud'               => 'nullable|numeric|between:-90,90',
            'longitud'              => 'nullable|numeric|between:-180,180',
        ]);

        $vendedor = $request->user();
        
        // ✅ MEJORA 1: Validar que el vendedor tenga almacén asignado
        $almacenId = optional($vendedor->almacen)->id ?? $vendedor->almacen_id;
        
        if (!$almacenId) {
            return response()->json([
                'message' => 'No tienes un almacén asignado. Contacta al administrador.'
            ], 422);
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        
        // ✅ MEJORA 2: Validar que el cliente esté asignado al vendedor
        if ($cliente->asignado_a !== $vendedor->id) {
            \Log::warning('Intento de venta a cliente no asignado', [
                'vendedor_id' => $vendedor->id,
                'cliente_id' => $cliente->id,
                'cliente_asignado_a' => $cliente->asignado_a,
            ]);
            
            return response()->json([
                'message' => 'No tienes permiso para vender a este cliente.'
            ], 403);
        }
        
        $nivelId = $cliente->nivel_precio_id;

        /** ---------- Idempotencia: si ya existe una venta con ese client_tx_id, regresa la misma ---------- */
        $clientTxId = $request->input('client_tx_id');
        if ($clientTxId) {
            $prev = Venta::where('client_tx_id', $clientTxId)->first();
            if ($prev) {
                \Log::info('Venta duplicada detectada (idempotencia)', [
                    'client_tx_id' => $clientTxId,
                    'venta_id' => $prev->id,
                ]);
                
                return response()->json([
                    'message'         => 'Venta ya registrada (reintento).',
                    'venta_id'        => $prev->id,
                    'total'           => (float) $prev->total,
                    'pagado'          => (float) $prev->total_pagado,
                    'saldo_pendiente' => (float) $prev->saldo_pendiente,
                    'estado'          => $prev->estado,
                    'warning'         => $this->creditWarning,
                ], 200);
            }
        }

        /** ---------- Política de crédito ---------- */
        $policy = config('ventas.credit_policy', 'strict'); // strict|warn
        $limit  = (float) config('ventas.credit_limit', 0); // 0 = sin límite

        $saldoPendienteCliente = Venta::where('cliente_id', $cliente->id)
            ->whereIn('estado', ['credito', 'parcial'])
            ->sum('saldo_pendiente');

        $existeCreditoPendiente = $saldoPendienteCliente > 0;

        if ($policy === 'strict' && $existeCreditoPendiente) {
            return response()->json([
                'message' => "El cliente tiene saldo pendiente ($" . number_format($saldoPendienteCliente, 2) . "). Debe liquidar o abonar antes de una nueva venta."
            ], 422);
        }

        if ($limit > 0 && $saldoPendienteCliente > $limit) {
            if ($policy === 'strict') {
                return response()->json([
                    'message' => "Saldo pendiente $" . number_format($saldoPendienteCliente, 2) .
                        " supera el límite de crédito $" . number_format($limit, 2) . "."
                ], 422);
            }
            $this->creditWarning = "Saldo pendiente $" . number_format($saldoPendienteCliente, 2) .
                " supera el límite de crédito $" . number_format($limit, 2) . ".";
        } elseif ($policy === 'warn' && $existeCreditoPendiente) {
            $this->creditWarning = "El cliente tiene saldo pendiente: $" . number_format($saldoPendienteCliente, 2) . ".";
        }

        // ✅ MEJORA 3: Logs detallados al inicio
        \Log::info('Iniciando creación de venta', [
            'vendedor_id' => $vendedor->id,
            'vendedor_nombre' => $vendedor->name,
            'cliente_id' => $cliente->id,
            'cliente_nombre' => $cliente->nombre,
            'almacen_id' => $almacenId,
            'client_tx_id' => $clientTxId,
            'productos_count' => count($request->productos ?? []),
            'promociones_count' => count($request->promociones ?? []),
            'es_credito' => $request->boolean('es_credito'),
        ]);

        try {
            $result = DB::transaction(function () use ($request, $vendedor, $almacenId, $nivelId, $clientTxId, $cliente) {

                $total = 0.0;

                // 1) Promos: validar stock + acumular total
                foreach ($request->promociones ?? [] as $promoData) {
                    $promo = Promocion::with('productos')->find($promoData['promocion_id']);
                    $veces = (int) $promoData['cantidad'];

                    if (!$promo || !$promo->activo) {
                        abort(422, "Promoción inválida o inactiva.");
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

                    $total += ((float) $promo->precio) * $veces;
                }

                // 2) Productos sueltos: validar stock + total con precio por nivel
                foreach ($request->productos ?? [] as $item) {
                    $stockTotal = Inventario::where('almacen_id', $almacenId)
                        ->where('producto_id', $item['producto_id'])
                        ->sum('cantidad');

                    if ($stockTotal < (int)$item['cantidad']) {
                        $producto = Producto::find($item['producto_id']);
                        abort(422, "Stock insuficiente para '{$producto->nombre}'. Disponible: {$stockTotal}");
                    }

                    $precioUnit = $this->precioCliente((int)$item['producto_id'], $nivelId);
                    $total += ((int)$item['cantidad']) * $precioUnit;
                }

                // ✅ MEJORA 4: Log del total calculado
                \Log::info('Total de venta calculado', [
                    'total' => $total,
                    'nivel_precio_id' => $nivelId,
                ]);

                // 3) Validar pagos vs total y crédito
                $pagos       = collect($request->pagos ?? [])->filter(fn($p) => ($p['monto'] ?? 0) > 0);
                $sumaPagos   = (float) $pagos->sum('monto');
                $esCredito   = (bool) $request->boolean('es_credito');
                $fv          = $request->filled('fecha_vencimiento')
                                ? Carbon::parse($request->fecha_vencimiento)
                                : null;

                $eps = 0.5;
                if (!$esCredito) {
                    if (abs($sumaPagos - $total) > $eps) {
                        abort(422, "Los pagos deben cubrir el total de la venta. Total: $total, Pagos: $sumaPagos");
                    }
                } else {
                    if ($sumaPagos - $total > $eps) {
                        abort(422, "Los pagos no pueden superar el total. Total: $total, Pagos: $sumaPagos");
                    }
                }

                $saldoPendiente = max(0, $total - $sumaPagos);
                $estado = ($saldoPendiente <= 0.01) ? 'pagada'
                    : ($esCredito ? 'credito' : 'parcial');

                // 4) Crear venta
                $venta = Venta::create([
                    'cliente_id'         => $cliente->id,
                    'vendedor_id'        => $vendedor->id,
                    'fecha'              => now(),
                    'total'              => $total,
                    'total_pagado'       => $sumaPagos,
                    'saldo_pendiente'    => $saldoPendiente,
                    'estado'             => $estado,
                    'es_credito'         => $esCredito,
                    'fecha_vencimiento'  => $fv,
                    'observaciones'      => $request->observaciones,
                    'client_tx_id'       => $clientTxId,
                ]);

                // ✅ MEJORA 5: Log de venta creada
                \Log::info('Venta creada en base de datos', [
                    'venta_id' => $venta->id,
                    'total' => $venta->total,
                    'estado' => $venta->estado,
                ]);

                // 5) Guardar promociones usadas
                foreach ($request->promociones ?? [] as $promoData) {
                    $promo = Promocion::find($promoData['promocion_id']);
                    VentaPromocion::create([
                        'venta_id'        => $venta->id,
                        'promocion_id'    => $promo->id,
                        'cantidad'        => (int) $promoData['cantidad'],
                        'precio_promocion'=> (float) $promo->precio,
                    ]);
                }

                // 6) Descontar productos sueltos del inventario (FIFO)
                foreach ($request->productos ?? [] as $item) {
                    $pid = (int) $item['producto_id'];
                    $qty = (int) $item['cantidad'];
                    $precioUnit = $this->precioCliente($pid, $nivelId);

                    $lotes = Inventario::where('almacen_id', $almacenId)
                        ->where('producto_id', $pid)
                        ->where('cantidad', '>', 0)
                        ->orderBy('fecha_caducidad')
                        ->get();

                    $remaining = $qty;
                    foreach ($lotes as $inv) {
                        if ($remaining <= 0) break;

                        $needThisLot = min($remaining, (int) $inv->cantidad);
                        $inv->cantidad -= $needThisLot;
                        
                        // ✅ MEJORA 6: Validar stock negativo
                        if ($inv->cantidad < 0) {
                            \Log::error('Stock quedó negativo', [
                                'inventario_id' => $inv->id,
                                'producto_id' => $pid,
                                'cantidad_final' => $inv->cantidad,
                                'venta_id' => $venta->id,
                            ]);
                            
                            abort(500, 'Error interno: Stock quedó negativo. Contacta al administrador.');
                        }
                        
                        $inv->save();

                        DetalleVenta::create([
                            'venta_id'        => $venta->id,
                            'producto_id'     => $pid,
                            'cantidad'        => $needThisLot,
                            'precio_unitario' => $precioUnit,
                            'subtotal'        => $needThisLot * $precioUnit,
                            'almacen_id'      => $almacenId,
                            'lote'            => $inv->lote,
                            'fecha_caducidad' => $inv->fecha_caducidad,
                        ]);

                        $remaining -= $needThisLot;
                    }

                    if ($remaining > 0) {
                        abort(422, "No fue posible descontar toda la cantidad del producto ID $pid.");
                    }
                }

                // 7) Descontar productos de promociones
                foreach ($request->promociones ?? [] as $promoData) {
                    $promo = Promocion::with('productos')->find($promoData['promocion_id']);
                    $veces = (int) $promoData['cantidad'];

                    foreach ($promo->productos as $producto) {
                        $needQty = (int)($producto->pivot->cantidad ?? 1) * $veces;

                        $lotes = Inventario::where('almacen_id', $almacenId)
                            ->where('producto_id', $producto->id)
                            ->where('cantidad', '>', 0)
                            ->orderBy('fecha_caducidad')
                            ->get();

                        $remaining = $needQty;
                        foreach ($lotes as $inv) {
                            if ($remaining <= 0) break;

                            $needThisLot = min($remaining, (int) $inv->cantidad);
                            $inv->cantidad -= $needThisLot;
                            
                            // ✅ Validar stock negativo en promociones también
                            if ($inv->cantidad < 0) {
                                \Log::error('Stock quedó negativo en promoción', [
                                    'inventario_id' => $inv->id,
                                    'producto_id' => $producto->id,
                                    'promocion_id' => $promo->id,
                                ]);
                                abort(500, 'Error interno: Stock quedó negativo en promoción.');
                            }
                            
                            $inv->save();

                            DetalleVenta::create([
                                'venta_id'        => $venta->id,
                                'producto_id'     => $producto->id,
                                'cantidad'        => $needThisLot,
                                'precio_unitario' => (float) $promo->precio / count($promo->productos),
                                'subtotal'        => ($needThisLot * (float) $promo->precio) / count($promo->productos),
                                'almacen_id'      => $almacenId,
                                'lote'            => $inv->lote,
                                'fecha_caducidad' => $inv->fecha_caducidad,
                                'promocion_id'    => $promo->id,
                            ]);

                            $remaining -= $needThisLot;
                        }
                    }
                }

                // 8) Procesar rechazos
                if ($request->filled('rechazos_ids')) {
                    foreach ($request->rechazos_ids as $rid) {
                        $rechazo = RechazoTemporal::find($rid);
                        if (!$rechazo) continue;

                        $rechazo->update(['venta_id' => $venta->id]);

                        $inv = Inventario::firstOrCreate([
                            'producto_id' => $rechazo->producto_id,
                            'almacen_id'  => 3, // Almacén de rechazos
                            'lote'        => $rechazo->lote,
                        ]);
                        $inv->cantidad = (float)($inv->cantidad ?? 0) + (float)$rechazo->cantidad;
                        $inv->fecha_caducidad = $rechazo->fecha_caducidad ?? $inv->fecha_caducidad;
                        $inv->save();
                    }
                }

                // 9) Guardar pagos recibidos
                foreach ($pagos as $pago) {
                    PagoVenta::create([
                        'venta_id'    => $venta->id,
                        'metodo'      => $pago['metodo'],
                        'monto'       => (float) $pago['monto'],
                        'referencia'  => $pago['referencia'] ?? null,
                        'cobrador_id' => $vendedor->id,
                        
                    ]);
                }
                
                // 10) Vincular con visita automáticamente
                $this->vincularConVisita(
                    $venta, 
                    $request->input('latitud'), 
                    $request->input('longitud')
                );

                return [
                    'venta_id'        => $venta->id,
                    'total'           => $total,
                    'suma_pagos'      => $sumaPagos,
                    'saldo_pendiente' => $saldoPendiente,
                    'estado'          => $estado,
                ];
            });

            // ✅ MEJORA 7: Log de éxito
            \Log::info('Venta registrada exitosamente', [
                'venta_id' => $result['venta_id'],
                'total' => $result['total'],
                'estado' => $result['estado'],
                'client_tx_id' => $clientTxId,
            ]);

            return response()->json([
                'message'         => 'Venta registrada correctamente.',
                'venta_id'        => $result['venta_id'],
                'total'           => $result['total'],
                'pagado'          => $result['suma_pagos'],
                'saldo_pendiente' => $result['saldo_pendiente'],
                'estado'          => $result['estado'],
                'warning'         => $this->creditWarning,
            ], 201);

        } catch (\Throwable $e) {
            \Log::error('Venta store failed', [
                'err'   => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tx'    => $clientTxId,
                'vendedor_id' => $vendedor->id,
                'cliente_id' => $request->cliente_id,
            ]);

            if ($clientTxId && str_contains(strtolower($e->getMessage()), 'unique')) {
                if ($prev = Venta::where('client_tx_id', $clientTxId)->first()) {
                    return response()->json([
                        'message'         => 'Venta ya registrada (reintento).',
                        'venta_id'        => $prev->id,
                        'total'           => (float) $prev->total,
                        'pagado'          => (float) $prev->total_pagado,
                        'saldo_pendiente' => (float) $prev->saldo_pendiente,
                        'estado'          => $prev->estado,
                        'warning'         => $this->creditWarning,
                    ], 200);
                }
            }

            return response()->json([
                'message' => 'Error al registrar la venta.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Vincular venta con visita automáticamente
     */
    private function vincularConVisita(Venta $venta, $latitud = null, $longitud = null)
    {
        try {
            $hoy = now()->toDateString();

            $visita = VisitaCliente::where('user_id', $venta->vendedor_id)
                ->where('cliente_id', $venta->cliente_id)
                ->whereDate('fecha_visita', $hoy)
                ->first();

            if ($visita) {
                $visita->update([
                    'venta_id' => $venta->id,
                    'realizo_venta' => true,
                    'motivo_no_venta' => null,
                    'latitud' => $latitud ?? $visita->latitud,
                    'longitud' => $longitud ?? $visita->longitud,
                ]);
                
                \Log::info("Visita #{$visita->id} vinculada con venta #{$venta->id}");
            } else {
                $nuevaVisita = VisitaCliente::create([
                    'user_id' => $venta->vendedor_id,
                    'cliente_id' => $venta->cliente_id,
                    'fecha_visita' => $hoy,
                    'hora_visita' => now()->toTimeString(),
                    'realizo_venta' => true,
                    'venta_id' => $venta->id,
                    'motivo_no_venta' => null,
                    'observaciones' => 'Visita registrada automáticamente desde venta móvil',
                    'latitud' => $latitud,
                    'longitud' => $longitud,
                    'estado' => 'visitado',
                ]);
                
                \Log::info("Visita #{$nuevaVisita->id} creada automáticamente para venta #{$venta->id}");
            }
        } catch (\Exception $e) {
            \Log::warning('No se pudo vincular venta con visita: ' . $e->getMessage(), [
                'venta_id' => $venta->id,
                'cliente_id' => $venta->cliente_id,
            ]);
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

        // ✅ MEJORA 8: Validar que el abono no sea mayor al saldo
        $montoAbono = (float) $request->monto;
        $saldoActual = (float) $venta->saldo_pendiente;

        if ($montoAbono > $saldoActual + 0.01) { // Tolerancia de 1 centavo
            return response()->json([
                'message' => 'El monto del abono ($' . number_format($montoAbono, 2) . 
                             ') no puede ser mayor al saldo pendiente ($' . number_format($saldoActual, 2) . ').',
            ], 422);
        }

        $vendedor = $request->user();

        // ✅ MEJORA 9: Log de abono
        \Log::info('Registrando abono a venta', [
            'venta_id' => $venta->id,
            'monto' => $montoAbono,
            'metodo' => $request->metodo,
            'cobrador_id' => $vendedor->id,
        ]);

        return DB::transaction(function () use ($request, $venta, $vendedor, $montoAbono) {

            PagoVenta::create([
                'venta_id'    => $venta->id,
                'metodo'      => $request->metodo,
                'monto'       => $montoAbono,
                'referencia'  => $request->referencia,
                'cobrador_id' => $vendedor->id,
            ]);

            $totalPagado = (float) $venta->pagos()->sum('monto');
            $saldo       = max(0, (float) $venta->total - $totalPagado);

            $venta->update([
                'total_pagado'    => $totalPagado,
                'saldo_pendiente' => $saldo,
                'estado'          => $saldo <= 0.01 ? 'pagada' : 'parcial',
            ]);

            \Log::info('Abono registrado exitosamente', [
                'venta_id' => $venta->id,
                'total_pagado' => $totalPagado,
                'saldo_pendiente' => $saldo,
                'nuevo_estado' => $venta->estado,
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