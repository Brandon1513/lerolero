<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\VisitaCliente;

class ClienteMovilController extends Controller
{
    /**
     * Lista de clientes asignados al vendedor.
     * - Si no viene ?all=1: solo los del día de visita.
     * - Incluye nivel de precio y saldo pendiente total (credito/parcial).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $diaActual = now()->locale('es')->isoFormat('dddd'); // "lunes"
        $diaTitulo = ucfirst($diaActual);                    // "Lunes"

        // Subquery para sumar saldo por cliente (solo ventas con estado credito/parcial)
        $saldoSub = DB::table('ventas')
            ->selectRaw('cliente_id, SUM(saldo_pendiente) as saldo')
            ->whereIn('estado', ['credito', 'parcial'])
            ->groupBy('cliente_id');

        $q = Cliente::query()
            ->where('asignado_a', $user->id)
            ->where('clientes.activo', true)
            ->leftJoinSub($saldoSub, 'v', function ($join) {
                $join->on('v.cliente_id', '=', 'clientes.id');
            })
            ->with(['nivelPrecio:id,nombre'])
            ->orderBy('clientes.nombre');

        if (!$request->boolean('all')) {
            $q->whereJsonContains('dias_visita', $diaTitulo);
        }

        // Solo campos necesarios + el saldo calculado
        $clientes = $q->get([
            'clientes.id',
            'clientes.nombre',
            'clientes.telefono',
            'clientes.latitud',
            'clientes.longitud',
            'clientes.nivel_precio_id',
            DB::raw('COALESCE(v.saldo,0) as saldo_pendiente_total'),
        ]);

        $payload = $clientes->map(function ($c) {
            return [
                'id'        => $c->id,
                'nombre'    => $c->nombre,
                'telefono'  => $c->telefono,
                'latitud'   => $c->latitud,
                'longitud'  => $c->longitud,
                'nivel_precio' => $c->nivelPrecio
                    ? ['id' => $c->nivelPrecio->id, 'nombre' => $c->nivelPrecio->nombre]
                    : null,

                // 👇 nuevo para la app
                'saldo_pendiente_total' => (float) $c->saldo_pendiente_total,
                'bloqueado'             => (float) $c->saldo_pendiente_total > 0,
            ];
        })->values();

        return response()->json($payload);
    }

    /**
     * Solo clientes del día (si quieres mantener este endpoint).
     * 🆕 AHORA incluye info de si ya fueron visitados HOY
     */
    public function delDia(Request $request)
    {
        $user = $request->user();
        $dia = ucfirst(now()->locale('es')->isoFormat('dddd'));
        $hoy = now()->toDateString();

        $clientes = Cliente::where('asignado_a', $user->id)
                    ->where('activo', true)
                    ->whereJsonContains('dias_visita', $dia)
                    ->get();

        // 🆕 Consultar cuáles ya fueron visitados hoy
        $visitadosHoy = VisitaCliente::where('user_id', $user->id)
            ->whereDate('fecha_visita', $hoy)
            ->pluck('cliente_id')
            ->toArray();

        // 🆕 Agregar flag "ya_visitado" a cada cliente
        $payload = $clientes->map(function ($c) use ($visitadosHoy) {
            return [
                'id'          => $c->id,
                'nombre'      => $c->nombre,
                'telefono'    => $c->telefono,
                'latitud'     => $c->latitud,
                'longitud'    => $c->longitud,
                'ya_visitado' => in_array($c->id, $visitadosHoy), // 👈 NUEVO
            ];
        });

        return response()->json($payload);
    }

    /**
     * Historial de ventas del cliente con relaciones.
     */
   public function ventas($id)
{
    $cliente = Cliente::findOrFail($id);

    $ventas = $cliente->ventas()
        ->with([
            'detalles.producto',
            'cliente',
            'pagos',
        ])
        ->orderByDesc('fecha')
        ->orderByDesc('id')
        ->get();

    // ✅ Cargar rechazos temporales activos (aún no cerrados)
    $vendedorId = auth()->id();

    $ventaIds = $ventas->pluck('id')->toArray();

    $rechazosTemporales = \App\Models\RechazoTemporal::with(['producto:id,nombre', 'detalles.producto:id,nombre'])
        ->where('vendedor_id', $vendedorId)
        ->whereIn('venta_id', $ventaIds)
        ->get();

    // Índice: venta_id => [rechazos temporales]
    $cambiosPorVenta = [];
    foreach ($rechazosTemporales as $r) {
        $vid = (int) $r->venta_id;
        if ($vid > 0) {
            $cambiosPorVenta[$vid][] = [
                'nombre'          => optional($r->producto)->nombre,
                'cantidad'        => (float) $r->cantidad,
                'motivo'          => $r->motivo,
                'lote'            => $r->lote,
                'fecha_caducidad' => $r->fecha_caducidad,
                'sustituciones'   => $r->detalles->map(fn($d) => [
                    'nombre'          => optional($d->producto)->nombre,
                    'cantidad'        => (float) $d->cantidad,
                    'lote'            => $d->lote,
                    'fecha_caducidad' => $d->fecha_caducidad,
                ])->toArray(),
            ];
        }
    }

    // ✅ Complementar con cierres históricos (rechazos ya procesados)
    $cierres = \App\Models\CierreRuta::where('vendedor_id', $vendedorId)
        ->whereNotNull('cambios')
        ->get();

    foreach ($cierres as $cierre) {
        $todosCambios = $cierre->cambios;
        if (is_string($todosCambios)) {
            $todosCambios = json_decode($todosCambios, true) ?? [];
        }
        foreach ((array) $todosCambios as $c) {
            $vid = (int) ($c['venta_id'] ?? 0);
            // Solo agregar si no está ya cubierto por rechazos_temporales
            if ($vid > 0 && !isset($cambiosPorVenta[$vid])) {
                $cambiosPorVenta[$vid][] = [
                    'nombre'          => $c['nombre'] ?? null,
                    'cantidad'        => (float) ($c['cantidad'] ?? 0),
                    'motivo'          => $c['motivo'] ?? null,
                    'lote'            => $c['lote'] ?? null,
                    'fecha_caducidad' => $c['fecha_caducidad'] ?? null,
                    'sustituciones'   => array_map(fn($s) => [
                        'nombre'          => $s['nombre'] ?? null,
                        'cantidad'        => (float) ($s['cantidad'] ?? 0),
                        'lote'            => $s['lote'] ?? null,
                        'fecha_caducidad' => $s['fecha_caducidad'] ?? null,
                    ], $c['sustituciones'] ?? []),
                ];
            }
        }
    }

    $payload = $ventas->map(function ($v) use ($cambiosPorVenta) {

        $metodos    = $v->pagos->pluck('metodo')->filter()->unique()->values()->all();
        $metodoPago = null;
        if ($v->es_credito && (float)$v->saldo_pendiente > 0) {
            $metodoPago = 'credito';
        } elseif (count($metodos) === 0) {
            $metodoPago = $v->es_credito ? 'credito' : 'efectivo';
        } elseif (count($metodos) === 1) {
            $metodoPago = $metodos[0];
        } else {
            $metodoPago = 'mixto';
        }

        $ref = optional(
            $v->pagos->firstWhere('metodo', 'transferencia')
            ?? $v->pagos->firstWhere('metodo', 'tarjeta')
        )->referencia ?? $v->nota_pago;

        $total          = (float) $v->total;
        $totalPagado    = (float) ($v->total_pagado ?? $v->pagos->sum('monto'));
        $saldoPendiente = max(0, (float) ($v->saldo_pendiente ?? ($total - $totalPagado)));

        // Si tiene saldo pendiente: "parcial" si ya hubo algún pago, "credito" si no hubo ninguno
        $estado = $saldoPendiente > 0
            ? ($totalPagado > 0 ? 'parcial' : 'credito')
            : 'pagada';

        // ✅ Cambios del cierre (no de rechazos_temporales que ya fueron borrados)
        $rechazos = $cambiosPorVenta[$v->id] ?? [];

        return [
            'id'                => $v->id,
            'fecha'             => optional($v->fecha)->toDateTimeString(),
            'total'             => $total,
            'observaciones'     => $v->observaciones,
            'estado'            => $estado,
            'es_credito'        => (bool) $v->es_credito,
            'total_pagado'      => $totalPagado,
            'saldo_pendiente'   => $saldoPendiente,
            'fecha_vencimiento' => optional($v->fecha_vencimiento)->toDateString(),
            'nota_pago'         => $ref,
            'metodo_pago'       => $metodoPago,

            'cliente'  => ['id' => $v->cliente->id, 'nombre' => $v->cliente->nombre],

            'detalles' => $v->detalles->map(fn($d) => [
                'producto_id'     => $d->producto_id,
                'producto'        => ['id' => $d->producto_id, 'nombre' => optional($d->producto)->nombre],
                'cantidad'        => (float) $d->cantidad,
                'precio_unitario' => (float) $d->precio_unitario,
                'subtotal'        => (float) $d->subtotal,
                'lote'            => $d->lote,
                'fecha_caducidad' => $d->fecha_caducidad,
            ])->values(),

            // Rechazos: temporales activos primero, históricos del cierre como respaldo
            'rechazos' => $rechazos,
        ];
    })->values();

    return response()->json($payload);
}
}