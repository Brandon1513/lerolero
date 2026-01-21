<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CierreRuta;
use App\Models\User;
use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Traslado;
use App\Models\DetalleTraslado;
use App\Models\RechazoTemporal;
use App\Models\Almacen;
use App\Models\Venta;
use App\Models\PagoVenta;
use Carbon\Carbon;

class CierreRutaController extends Controller
{
    public function index(Request $request)
{
    $cierres = CierreRuta::with('vendedor', 'cerradoPor')
        ->when($request->vendedor_id, fn($q) => $q->where('vendedor_id', $request->vendedor_id))
        ->when($request->fecha_inicio, fn($q) => $q->whereDate('fecha', '>=', $request->fecha_inicio))
        ->when($request->fecha_fin, fn($q) => $q->whereDate('fecha', '<=', $request->fecha_fin))
        ->when($request->estatus, fn($q) => $q->where('estatus', $request->estatus))
        ->when($request->cerrado_por, fn($q) => $q->where('cerrado_por', $request->cerrado_por))
        ->orderBy('fecha', 'desc')
        ->paginate(10);

    $vendedores = User::role('vendedor')->get();
    $admins     = User::role('administrador')->get();

    // ✅ Resumen por cierre (solo los de la página actual)
    $resumenIndex = [];
    foreach ($cierres as $cierre) {
        $resumenIndex[$cierre->id] = $this->buildResumenIndex($cierre);
    }

    return view('cierres.index', compact('cierres', 'vendedores', 'admins', 'resumenIndex'));
}

private function buildResumenIndex(CierreRuta $cierre): array
{
    $fecha = Carbon::parse($cierre->fecha)->toDateString();

    // =========================
    // 1) Ventas del día
    // =========================
    $ventasDiaTotal = (float) Venta::query()
        ->where('vendedor_id', $cierre->vendedor_id)
        ->whereDate('fecha', $fecha) // si no tienes 'fecha', usa created_at
        ->sum('total');

    // ✅ Pagos cobrados HOY por este vendedor (cobrador)
    $pagosHoyBase = PagoVenta::query()
        ->whereDate('created_at', $fecha)
        ->where('cobrador_id', $cierre->vendedor_id);

    // =========================
    // 2) Cobrado hoy (TOTAL)
    // =========================
    $cobradoHoyTotal = (float) (clone $pagosHoyBase)->sum('monto');

    // =========================
    // 3) Cobrado hoy (ventas del día)
    // =========================
    $cobradoHoyVentasDia = (float) (clone $pagosHoyBase)
        ->whereHas('venta', fn($q) => $q->whereDate('fecha', $fecha))
        ->sum('monto');

    // =========================
    // 4) Cobrado hoy (saldos anteriores)
    // =========================
    $cobradoHoySaldosAnteriores = (float) (clone $pagosHoyBase)
        ->whereHas('venta', fn($q) => $q->whereDate('fecha', '<', $fecha))
        ->sum('monto');

    // =========================
    // 5) Crédito del día (lo que quedó pendiente HOY)
    // =========================
    $creditoDiaTotal = max($ventasDiaTotal - $cobradoHoyVentasDia, 0);

    // =========================
    // 6) Chips por método
    //    - metodos: cobrado hoy TOTAL
    //    - metodos_dia: cobrado hoy pero aplicado a ventas del día
    //    - metodos_anteriores: cobrado hoy aplicado a saldos anteriores
    // =========================
    $metodosTotal = (clone $pagosHoyBase)
        ->selectRaw('metodo, SUM(monto) as total')
        ->groupBy('metodo')
        ->pluck('total', 'metodo')
        ->toArray();

    $metodosDia = (clone $pagosHoyBase)
        ->whereHas('venta', fn($q) => $q->whereDate('fecha', $fecha))
        ->selectRaw('metodo, SUM(monto) as total')
        ->groupBy('metodo')
        ->pluck('total', 'metodo')
        ->toArray();

    $metodosAnterior = (clone $pagosHoyBase)
        ->whereHas('venta', fn($q) => $q->whereDate('fecha', '<', $fecha))
        ->selectRaw('metodo, SUM(monto) as total')
        ->groupBy('metodo')
        ->pluck('total', 'metodo')
        ->toArray();

    return [
        'ventas_dia_total'              => $ventasDiaTotal,
        'cobrado_hoy_total'             => $cobradoHoyTotal,
        'cobrado_hoy_ventas_dia'        => $cobradoHoyVentasDia,
        'cobrado_hoy_saldos_anteriores' => $cobradoHoySaldosAnteriores,
        'credito_dia_total'             => $creditoDiaTotal,

        // ✅ para chips en la columna “Cobrado hoy”
        'metodos'                       => $metodosTotal,

        // ✅ opcionales si luego quieres chips extra
        'metodos_dia'                   => $metodosDia,
        'metodos_anteriores'            => $metodosAnterior,
    ];
}

    public function show(CierreRuta $cierre)
{
    $cierre->load(['vendedor', 'cerradoPor']);

    $fecha = Carbon::parse($cierre->fecha)->toDateString();
    $vendedorId = $cierre->vendedor_id;

    // Ventas del día
    $ventasDia = Venta::with('cliente')
        ->where('vendedor_id', $vendedorId)
        ->whereDate('fecha', $fecha)
        ->get();

    $ventasDiaIds = $ventasDia->pluck('id')->all();

    $totalVentasDia          = (float) $ventasDia->sum('total');
    $totalPagadoEnVentasDia  = (float) $ventasDia->sum('total_pagado');
    $saldoPendienteDia       = (float) $ventasDia->sum('saldo_pendiente'); // ✅ lo que se fue a crédito HOY

    // ✅ NUEVO: saldo pendiente acumulado del vendedor (todas las fechas)
    $saldoPendienteAcumulado = (float) Venta::where('vendedor_id', $vendedorId)
        ->whereIn('estado', ['credito', 'parcial'])
        ->sum('saldo_pendiente');

    // Pagos cobrados HOY (pueden ser de ventas hoy o de ventas pasadas)
    $pagosHoy = PagoVenta::with('venta.cliente')
        ->where('cobrador_id', $vendedorId)
        ->whereDate('created_at', $fecha) // ✅ ojo: tu tabla no tiene columna "fecha", usa created_at
        ->get();

    $totalCobradoHoy = (float) $pagosHoy->sum('monto');

    // Separar pagos hoy: a ventas del día vs a ventas anteriores
    $pagosHoyVentasDia        = $pagosHoy->filter(fn($p) => in_array($p->venta_id, $ventasDiaIds));
    $pagosHoyVentasAnteriores = $pagosHoy->filter(fn($p) => !in_array($p->venta_id, $ventasDiaIds));

    $totalCobradoHoyVentasDia        = (float) $pagosHoyVentasDia->sum('monto');
    $totalCobradoHoyVentasAnteriores = (float) $pagosHoyVentasAnteriores->sum('monto');

    // Desglose por método (HOY)
    $metodos = ['efectivo', 'transferencia', 'tarjeta'];

    $metodosHoy = collect($metodos)->mapWithKeys(fn($m) => [
        $m => (float) $pagosHoy->where('metodo', $m)->sum('monto')
    ])->toArray();

    $metodosHoyVentasDia = collect($metodos)->mapWithKeys(fn($m) => [
        $m => (float) $pagosHoyVentasDia->where('metodo', $m)->sum('monto')
    ])->toArray();

    $metodosHoyVentasAnteriores = collect($metodos)->mapWithKeys(fn($m) => [
        $m => (float) $pagosHoyVentasAnteriores->where('metodo', $m)->sum('monto')
    ])->toArray();

    // Para cuadrar caja: efectivo esperado hoy vs efectivo entregado (cuando cierre)
    $efectivoEsperadoHoy = (float) ($metodosHoy['efectivo'] ?? 0);

    // Clientes con saldo pendiente DEL DÍA
    $clientesPendientesDia = $ventasDia
        ->filter(fn($v) => (float)$v->saldo_pendiente > 0)
        ->groupBy('cliente_id')
        ->map(function ($rows) {
            $first = $rows->first();
            return [
                'cliente'   => $first->cliente?->nombre ?? '—',
                'ventas'    => $rows->count(),
                'pendiente' => (float) $rows->sum('saldo_pendiente'),
            ];
        })->values();

    // Pagos de saldos anteriores agrupados por cliente
    $cobranzaAnteriorPorCliente = $pagosHoyVentasAnteriores
        ->groupBy(fn($p) => $p->venta?->cliente_id)
        ->map(function ($rows) {
            $first = $rows->first();
            return [
                'cliente'            => $first->venta?->cliente?->nombre ?? '—',
                'monto'              => (float) $rows->sum('monto'),
                'ventas_involucradas'=> $rows->pluck('venta_id')->unique()->count(),
            ];
        })->values();

    // ✅ Detalle de pagos cobrados hoy (para tu tabla toggle)
    $pagosHoyDetalle = $pagosHoy->map(function ($p) {
        return [
            'cliente'     => $p->venta?->cliente?->nombre ?? '—',
            'venta_id'    => $p->venta_id,
            'fecha_venta' => optional($p->venta?->fecha)->format('d/m/Y') ?? '—',
            'fecha_cobro' => optional($p->created_at)->format('d/m/Y H:i') ?? '—',
            'metodo'      => $p->metodo,
            'monto'       => (float) $p->monto,
            'referencia'  => $p->referencia,
        ];
    });

    $resumen = [
        'ventas_dia' => [
            'total_ventas'           => $totalVentasDia,
            'total_pagado_en_ventas' => $totalPagadoEnVentasDia,
            'saldo_pendiente'        => $saldoPendienteDia,
            'count_credito'          => $ventasDia->where('estado', 'credito')->count(),
            'count_parcial'          => $ventasDia->where('estado', 'parcial')->count(),
            'count_pagada'           => $ventasDia->where('estado', 'pagada')->count(),
        ],
        'cobros_hoy' => [
            'total'                   => $totalCobradoHoy,
            'ventas_dia'              => $totalCobradoHoyVentasDia,
            'ventas_anteriores'       => $totalCobradoHoyVentasAnteriores,
            'metodos'                 => $metodosHoy,
            'metodos_ventas_dia'      => $metodosHoyVentasDia,
            'metodos_ventas_anteriores'=> $metodosHoyVentasAnteriores,
            'efectivo_esperado'       => $efectivoEsperadoHoy,
        ],
    ];

    return view('cierres.show', compact(
        'cierre',
        'resumen',
        'clientesPendientesDia',
        'cobranzaAnteriorPorCliente',
        'pagosHoyDetalle',
        'saldoPendienteAcumulado'
    ));
}


    public function update(Request $request, CierreRuta $cierre)
{
    $request->validate([
        'total_efectivo' => 'required|numeric|min:0',
        'observaciones'  => 'nullable|string|max:1000',
    ]);

    $almacenVendedor = Almacen::where('tipo', 'vendedor')->where('user_id', $cierre->vendedor_id)->first();
    $almacenGeneral  = Almacen::where('tipo', 'general')->first();
    $almacenRechazo  = Almacen::where('tipo', 'rechazo')->first();

    if (!$almacenVendedor || !$almacenGeneral || !$almacenRechazo) {
        return redirect()->route('cierres.index')->withErrors('Error al localizar almacenes.');
    }

    // =========================
    // ✅ EFECTIVO ESPERADO (HOY)
    // =========================
    $fecha = Carbon::parse($cierre->fecha)->toDateString();

    // Sumamos SOLO efectivo cobrado ese día por el vendedor (incluye ventas del día + abonos anteriores)
    $efectivoEsperadoHoy = (float) PagoVenta::where('cobrador_id', $cierre->vendedor_id)
        ->whereDate('created_at', $fecha)
        ->where('metodo', 'efectivo')
        ->sum('monto');

    // 1. Inventario final con lote y caducidad
    $inventarioFinal = Inventario::where('almacen_id', $almacenVendedor->id)
        ->get()
        ->map(function ($item) {
            return [
                'producto_id'     => $item->producto_id,
                'nombre'          => optional($item->producto)->nombre,
                'cantidad'        => $item->cantidad,
                'lote'            => $item->lote,
                'fecha_caducidad' => $item->fecha_caducidad,
            ];
        })->toArray();

    // 2. Inventario inicial
    $trasladoInicial = Traslado::where('almacen_destino_id', $almacenVendedor->id)
        ->whereDate('fecha', Carbon::parse($cierre->fecha)->toDateString())
        ->latest()
        ->first();

    $inventarioInicial = [];
    if ($trasladoInicial) {
        $inventarioInicial = DetalleTraslado::where('traslado_id', $trasladoInicial->id)
            ->get()
            ->map(function ($item) {
                return [
                    'producto_id'     => $item->producto_id,
                    'nombre'          => optional($item->producto)->nombre,
                    'cantidad'        => $item->cantidad,
                    'lote'            => $item->lote,
                    'fecha_caducidad' => $item->fecha_caducidad,
                ];
            })->toArray();
    }

    // 3. Procesar cambios
    $rechazos = RechazoTemporal::where('vendedor_id', $cierre->vendedor_id)->get();
    $listaCambios = [];

    foreach ($rechazos as $rechazo) {
        $producto = Producto::find($rechazo->producto_id);
        if (!$producto) continue;

        $trasladoRechazo = Traslado::create([
            'almacen_origen_id'  => $almacenVendedor->id,
            'almacen_destino_id' => $almacenRechazo->id,
            'fecha'              => now(),
            'observaciones'      => 'Producto rechazado por ' . $rechazo->motivo,
            'user_id'            => auth()->id(),
        ]);

        DetalleTraslado::create([
            'traslado_id' => $trasladoRechazo->id,
            'producto_id' => $rechazo->producto_id,
            'cantidad'    => $rechazo->cantidad,
        ]);

        $inventarioRechazo = Inventario::firstOrNew([
            'almacen_id'  => $almacenRechazo->id,
            'producto_id' => $rechazo->producto_id,
        ]);

        $inventarioRechazo->cantidad = (float)($inventarioRechazo->cantidad ?? 0) + (float)$rechazo->cantidad;
        $inventarioRechazo->save();

        $listaCambios[] = [
            'producto_id'     => $rechazo->producto_id,
            'nombre'          => $producto->nombre,
            'cantidad'        => $rechazo->cantidad,
            'motivo'          => $rechazo->motivo,
            'lote'            => $rechazo->lote,
            'fecha_caducidad' => $rechazo->fecha_caducidad,
        ];

        $rechazo->delete();
    }

    // 4. Crear traslado de productos devueltos al almacén general
    $traslado = Traslado::create([
        'almacen_origen_id'  => $almacenVendedor->id,
        'almacen_destino_id' => $almacenGeneral->id,
        'fecha'              => now(),
        'observaciones'      => 'Devolución por cierre de ruta del vendedor ' . $cierre->vendedor->name,
        'user_id'            => auth()->id(),
    ]);

    foreach ($inventarioFinal as $item) {
        $producto = Producto::find($item['producto_id']);
        if (!$producto) continue;

        DetalleTraslado::create([
            'traslado_id' => $traslado->id,
            'producto_id' => $producto->id,
            'cantidad'    => $item['cantidad'],
        ]);

        $inventarioGeneral = Inventario::firstOrNew([
            'almacen_id'  => $almacenGeneral->id,
            'producto_id' => $producto->id,
        ]);

        $inventarioGeneral->cantidad = (float)($inventarioGeneral->cantidad ?? 0) + (float)$item['cantidad'];
        $inventarioGeneral->save();
    }

    // 5. Vaciar inventario del vendedor
    Inventario::where('almacen_id', $almacenVendedor->id)->delete();

    // 6. Actualizar cierre
    $cierre->update([
        'total_efectivo'     => $request->total_efectivo,
        'observaciones'      => $request->observaciones,
        'estatus'            => 'cuadrado',
        'cerrado_por'        => auth()->id(),
        'inventario_inicial' => $inventarioInicial,
        'inventario_final'   => $inventarioFinal,
        'cambios'            => $listaCambios,
        'traslado_id'        => $traslado->id,
    ]);

    // ✅ CUADRE REAL: entregado vs efectivo cobrado HOY
    $diferencia = (float) $cierre->total_efectivo - (float) $efectivoEsperadoHoy;

    // tolerancia por decimales
    $eps = 0.01;
    if (abs($diferencia) <= $eps) $diferencia = 0;

    $toast = $diferencia === 0 ? 'cuadrado' : ($diferencia < 0 ? 'faltan' : 'sobran');

    return redirect()
        ->route('cierres.index')
        ->with([
            'success' => 'Cierre completado correctamente.',
            'toast'   => $toast,
        ]);
}
}
