<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Preventa;
use App\Models\Producto;
use App\Models\ProductoNivelPrecio;
use App\Models\Promocion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Almacen;
use Illuminate\Support\Arr;


class PreventaController extends Controller
{
    private function precioCliente(int $productoId, ?int $nivelId): float
    {
        if ($nivelId) {
            $pp = ProductoNivelPrecio::where('producto_id', $productoId)
                ->where('nivel_precio_id', $nivelId)
                ->first();

            if ($pp && $pp->precio !== null) return (float) $pp->precio;
        }
        return (float) (Producto::find($productoId)?->precio ?? 0);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id'    => 'required|exists:clientes,id',
            'observaciones' => 'nullable|string',

            'productos'                   => 'nullable|array',
            'productos.*.producto_id'     => 'required_with:productos|exists:productos,id',
            'productos.*.cantidad'        => 'required_with:productos|integer|min:1',

            'promociones'                   => 'nullable|array',
            'promociones.*.promocion_id'    => 'required_with:promociones|exists:promociones,id',
            'promociones.*.cantidad'        => 'required_with:promociones|integer|min:1',

            'rechazos_ids'   => 'nullable|array',
            'rechazos_ids.*' => 'integer|exists:rechazos_temporales,id',

            'pagos'                 => 'nullable|array',
            'pagos.*.metodo'        => 'required_with:pagos|in:efectivo,transferencia,tarjeta',
            'pagos.*.monto'         => 'required_with:pagos|numeric|min:0',
            'pagos.*.referencia'    => 'nullable|string|max:191',
            'es_credito'            => 'nullable|boolean',
            'fecha_vencimiento'     => 'nullable|date',

            // idempotencia
            'client_tx_id'          => 'nullable|string|max:64',
        ]);

        // ✅ regla: debe venir algo para vender
        $productosCount   = count($request->productos ?? []);
        $promocionesCount = count($request->promociones ?? []);
        if ($productosCount === 0 && $promocionesCount === 0) {
            return response()->json([
                'message' => 'Debes agregar al menos un producto o una promoción para crear la preventa.'
            ], 422);
        }

        $vendedor = $request->user();

        $almacenId = optional($vendedor->almacen)->id ?? $vendedor->almacen_id;
        if (!$almacenId) {
            return response()->json(['message' => 'No tienes un almacén asignado.'], 422);
        }

        $cliente = Cliente::findOrFail($request->cliente_id);
        if ($cliente->asignado_a !== $vendedor->id) {
            return response()->json(['message' => 'No tienes permiso para vender a este cliente.'], 403);
        }

        $nivelId   = $cliente->nivel_precio_id;
        $clientTxId = $request->input('client_tx_id');

        return DB::transaction(function () use ($request, $vendedor, $almacenId, $nivelId, $cliente, $clientTxId) {

            /** ✅ Idempotencia: si ya existe una preventa con ese client_tx_id, regresa la misma */
            if ($clientTxId) {
                $prev = Preventa::where('client_tx_id', $clientTxId)
                    ->lockForUpdate()
                    ->first();

                if ($prev) {
                    return response()->json([
                        'message'     => 'Preventa ya registrada (reintento).',
                        'preventa_id' => $prev->id,
                        'folio'       => $prev->folio,
                        'total'       => (float) $prev->total,
                        'pagado'      => (float) $prev->total_pagado,
                        'saldo'       => (float) $prev->saldo_pendiente,
                        'status'      => $prev->status,
                    ], 200);
                }
            }

            $total = 0.0;

            // 1) Validar promos + total (sin descontar inventario)
            foreach ($request->promociones ?? [] as $promoData) {
                $promo = Promocion::with('productos')->find($promoData['promocion_id']);
                $veces = (int) $promoData['cantidad'];

                if (!$promo || !$promo->activo) abort(422, "Promoción inválida o inactiva.");

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

            // 2) Validar productos + total (sin descontar inventario)
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

            if ($total <= 0.0) {
                abort(422, 'El total de la preventa no puede ser 0.');
            }

            // 3) pagos / crédito
            $pagos     = collect($request->pagos ?? [])->filter(fn($p) => ($p['monto'] ?? 0) > 0);
            $pagado    = (float) $pagos->sum('monto');
            $esCredito = (bool) $request->boolean('es_credito');
            $fv        = $request->filled('fecha_vencimiento') ? Carbon::parse($request->fecha_vencimiento) : null;

            $eps = 0.5;
            if (!$esCredito) {
                if (abs($pagado - $total) > $eps) {
                    abort(422, "Los pagos deben cubrir el total. Total: $total, Pagos: $pagado");
                }
            } else {
                if ($pagado - $total > $eps) {
                    abort(422, "Los pagos no pueden superar el total. Total: $total, Pagos: $pagado");
                }
            }

            $saldo = max(0, $total - $pagado);

            // ✅ payload “limpio” (solo lo necesario)
            $payload = [
                'cliente_id'      => $cliente->id,
                'observaciones'   => $request->input('observaciones'),
                'productos'       => $request->input('productos', []),
                'promociones'     => $request->input('promociones', []),
                'rechazos_ids'    => $request->input('rechazos_ids', []),
                'pagos'           => $request->input('pagos', []),
                'es_credito'      => $esCredito,
                'fecha_vencimiento' => $request->input('fecha_vencimiento'),
            ];

            // 4) crear preventa
            $preventa = Preventa::create([
                'cliente_id'        => $cliente->id,
                'vendedor_id'       => $vendedor->id,
                'almacen_id'        => $almacenId,
                'total'             => $total,
                'total_pagado'      => $pagado,
                'saldo_pendiente'   => $saldo,
                'es_credito'        => $esCredito,
                'fecha_vencimiento' => $fv,
                'status'            => 'impresa',
                'printed_at'        => now(),
                'client_tx_id'      => $clientTxId,
                'payload'           => $payload,
                'venta_id'          => null,
            ]);

            // folio basado en ID (simple y robusto)
            $folio = 'PV-' . now()->format('Ymd') . '-' . str_pad((string)$preventa->id, 6, '0', STR_PAD_LEFT);
            $preventa->update(['folio' => $folio]);

            return response()->json([
                'message'     => 'Preventa creada.',
                'preventa_id' => $preventa->id,
                'folio'       => $preventa->folio,
                'total'       => (float) $preventa->total,
                'pagado'      => (float) $preventa->total_pagado,
                'saldo'       => (float) $preventa->saldo_pendiente,
                'es_credito'  => (bool) $preventa->es_credito,
                'fecha_vencimiento' => optional($preventa->fecha_vencimiento)->format('Y-m-d'),
                'status'      => $preventa->status,
            ], 201);
        });
    }
     public function show(Preventa $preventa)
{
    $user = request()->user();

    // Solo el vendedor dueño (ajústalo si RH/Admin también debe verlo)
    if ((int)$preventa->vendedor_id !== (int)$user->id) {
        return response()->json(['message' => 'No autorizado.'], 403);
    }

    $payload = is_array($preventa->payload) ? $preventa->payload : (json_decode($preventa->payload ?? '[]', true) ?: []);

    $cliente = Cliente::select('id','nombre')->find($preventa->cliente_id);

    // ====== Productos normales ======
    $productosReq = $payload['productos'] ?? [];
    $productoIds  = collect($productosReq)->pluck('producto_id')->filter()->unique()->values()->all();

    $productosDB = Producto::with('categoria:id,nombre')
        ->whereIn('id', $productoIds)
        ->get()
        ->keyBy('id');

    $productosTicket = collect($productosReq)->map(function ($it) use ($productosDB) {
        $pid = (int)($it['producto_id'] ?? 0);
        $pdb = $productosDB->get($pid);

        $precioUnit = (float)($it['precio_unitario'] ?? ($pdb?->precio ?? 0));

        return [
            'producto_id' => $pid,
            'cantidad' => (int)($it['cantidad'] ?? 0),
            'lote' => $it['lote'] ?? null,
            'fecha_caducidad' => $it['fecha_caducidad'] ?? null,
            'producto' => [
                'id' => $pid,
                'nombre' => $pdb?->nombre ?? "Producto #{$pid}",
                'precio' => $precioUnit, // 👈 importante para que calcule igual que ticket.tsx
                'categoria' => $pdb?->categoria ? [
                    'id' => $pdb->categoria->id,
                    'nombre' => $pdb->categoria->nombre,
                ] : null,
            ],
        ];
    })->values()->all();

    // ====== Promociones ======
    $promosReq = $payload['promociones'] ?? [];
    $promoIds  = collect($promosReq)->pluck('promocion_id')->filter()->unique()->values()->all();

    $promosDB = Promocion::with(['productos' => function($q){
            $q->select('productos.id','productos.nombre','productos.precio');
        }])
        ->whereIn('id', $promoIds)
        ->get()
        ->keyBy('id');

    $promosTicket = collect($promosReq)->map(function ($it) use ($promosDB) {
        $id = (int)($it['promocion_id'] ?? 0);
        $promo = $promosDB->get($id);

        return [
            'promocion_id' => $id,
            'cantidad' => (int)($it['cantidad'] ?? 0),
            'precio_promocion' => (float)($promo?->precio ?? 0),
            'nombre_promocion' => $promo?->nombre ?? "Promoción #{$id}",
            'productos' => $promo
                ? $promo->productos->map(function($p){
                    return [
                        'id' => $p->id,
                        'nombre' => $p->nombre,
                        'precio' => (float)$p->precio,
                        'pivot' => [
                            'cantidad' => (int)($p->pivot->cantidad ?? 1),
                        ],
                    ];
                })->values()->all()
                : [],
        ];
    })->values()->all();

    // Unificamos en el mismo shape que tu ticket usa (productos + promos en un array)
    $itemsTicket = array_merge($productosTicket, $promosTicket);

    return response()->json([
        'id' => $preventa->id,
        'folio' => $preventa->folio,
        'created_at' => optional($preventa->created_at)->toISOString(),
        'printed_at' => optional($preventa->printed_at)->toISOString(),
        'cliente' => $cliente ? ['id' => $cliente->id, 'nombre' => $cliente->nombre] : null,

        'observaciones' => $payload['observaciones'] ?? '',
        'pagos' => $payload['pagos'] ?? [],
        'es_credito' => (bool)($payload['es_credito'] ?? $preventa->es_credito),
        'fecha_vencimiento' => optional($preventa->fecha_vencimiento)->format('Y-m-d'),
        'nota_pago' => $payload['nota_pago'] ?? '',

        // 👇 ESTO es lo que va a usar el TicketPrevio
        'productos_ticket' => $itemsTicket,

        // totales guardados
        'total' => (float)$preventa->total,
        'total_pagado' => (float)$preventa->total_pagado,
        'saldo_pendiente' => (float)$preventa->saldo_pendiente,
        'status' => $preventa->status,
    ]);
}

public function markPrinted(Request $request, Preventa $preventa)
{
    $user = $request->user();

    if ((int)$preventa->vendedor_id !== (int)$user->id) {
        return response()->json(['message' => 'No tienes permiso.'], 403);
    }

    if (!empty($preventa->venta_id)) {
        return response()->json(['message' => 'Esta preventa ya fue convertida en venta, no se puede reimprimir como preventa.'], 422);
    }

    // ✅ marca como impresa (o reimpresa)
    $preventa->update([
        'status' => 'impresa',
        'printed_at' => now(),
    ]);

    return response()->json([
        'message' => 'Preventa marcada como impresa.',
        'preventa_id' => $preventa->id,
        'printed_at' => $preventa->printed_at?->toDateTimeString(),
        'status' => $preventa->status,
    ], 200);
}

/**
 * (Opcional) Listado para historial: /preventas?status=impresa&date=2026-02-09
 */
public function index(Request $request)
{
    $user = $request->user();

    $status = $request->query('status');
    $date   = $request->query('date'); // YYYY-MM-DD

    $q = Preventa::query()
        ->where('vendedor_id', $user->id)
        ->with(['cliente:id,nombre', 'venta:id,created_at,total,cliente_id,vendedor_id'])
        ->latest();

    if ($status) {
        $q->where('status', $status);
    }

    if ($date) {
        $q->whereDate('created_at', $date);
    }

    return response()->json($q->paginate(20));
}
}
