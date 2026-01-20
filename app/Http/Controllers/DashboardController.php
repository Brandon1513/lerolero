<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\VisitaCliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 📅 Período seleccionado (por defecto: mes actual)
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin    = $request->input('fecha_fin', now()->toDateString());

        // ✅ Filtro rápido de caducidad (7/15/30 días)
        $caducanEn = (int) $request->input('caducan_en', 30);
        $caducanEn = in_array($caducanEn, [7, 15, 30], true) ? $caducanEn : 30;

        // 💰 VENTAS
        $ventasQuery = Venta::whereBetween('fecha', [$fechaInicio, $fechaFin]);

        $totalVentas    = (float) $ventasQuery->sum('total');
        $cantidadVentas = (int) $ventasQuery->count();
        $ticketPromedio = $cantidadVentas > 0 ? $totalVentas / $cantidadVentas : 0;

        // Estado de ventas
        $ventasPagadas   = (int) $ventasQuery->clone()->where('estado', 'pagada')->count();
        $ventasCredito   = (int) $ventasQuery->clone()->where('estado', 'credito')->count();
        $ventasParciales = (int) $ventasQuery->clone()->where('estado', 'parcial')->count();

        $saldoPendienteTotal = (float) Venta::whereIn('estado', ['credito', 'parcial'])->sum('saldo_pendiente');

        // 📊 Ventas del día
        $ventasHoy        = (float) Venta::whereDate('fecha', today())->sum('total');
        $cantidadVentasHoy = (int) Venta::whereDate('fecha', today())->count();

        // 📈 Comparación con período anterior
        $diasPeriodo = Carbon::parse($fechaInicio)->diffInDays(Carbon::parse($fechaFin)) + 1;

        $fechaInicioAnterior = Carbon::parse($fechaInicio)->subDays($diasPeriodo)->toDateString();
        $fechaFinAnterior    = Carbon::parse($fechaInicio)->subDay()->toDateString();

        $ventasPeriodoAnterior = (float) Venta::whereBetween('fecha', [$fechaInicioAnterior, $fechaFinAnterior])->sum('total');

        $crecimiento = $ventasPeriodoAnterior > 0
            ? (($totalVentas - $ventasPeriodoAnterior) / $ventasPeriodoAnterior) * 100
            : 0;

        // 👥 TOP VENDEDORES
        $topVendedores = Venta::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->select('vendedor_id', DB::raw('SUM(total) as total_vendido'), DB::raw('COUNT(*) as num_ventas'))
            ->with('vendedor:id,name')
            ->whereNotNull('vendedor_id')
            ->groupBy('vendedor_id')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get()
            ->map(function ($v) {
                return [
                    'nombre' => $v->vendedor->name ?? 'N/D',
                    'total'  => (float) $v->total_vendido,
                    'ventas' => (int) $v->num_ventas,
                ];
            });

        // 🏆 TOP CLIENTES
        $topClientes = Venta::whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->select('cliente_id', DB::raw('SUM(total) as total_comprado'), DB::raw('COUNT(*) as num_compras'))
            ->with('cliente:id,nombre')
            ->groupBy('cliente_id')
            ->orderByDesc('total_comprado')
            ->limit(5)
            ->get()
            ->map(function ($v) {
                return [
                    'nombre'  => $v->cliente->nombre ?? 'N/D',
                    'total'   => (float) $v->total_comprado,
                    'compras' => (int) $v->num_compras,
                ];
            });

        // 📦 TOP PRODUCTOS
        $topProductos = DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin])
            ->select(
                'productos.nombre',
                DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'),
                DB::raw('SUM(detalle_ventas.subtotal) as ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get()
            ->map(function ($p) {
                return [
                    'nombre'   => $p->nombre,
                    'cantidad' => (float) $p->total_vendido,
                    'ingresos' => (float) $p->ingresos,
                ];
            });

        // 📊 VENTAS POR CATEGORÍA
        $ventasPorCategoria = DB::table('detalle_ventas')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->whereBetween('ventas.fecha', [$fechaInicio, $fechaFin])
            ->select('categorias.nombre as categoria', DB::raw('SUM(detalle_ventas.subtotal) as total'))
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderByDesc('total')
            ->get()
            ->map(function ($c) {
                return [
                    'categoria' => $c->categoria,
                    'total'     => (float) $c->total,
                ];
            });

        // 📅 VENTAS POR DÍA (últimos 30 días)
        $ventasPorDia = Venta::whereBetween('fecha', [now()->subDays(29)->toDateString(), now()->toDateString()])
            ->select(DB::raw('DATE(fecha) as fecha'), DB::raw('SUM(total) as total'), DB::raw('COUNT(*) as cantidad'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(function ($v) {
                return [
                    'fecha'    => Carbon::parse($v->fecha)->format('d/m'),
                    'total'    => (float) $v->total,
                    'cantidad' => (int) $v->cantidad,
                ];
            });

        // 🗺️ VISITAS Y CONVERSIÓN
$visitasStats = null;

if (DB::getSchemaBuilder()->hasTable('visitas_clientes')) {

    // Query base (rango de fechas)
    $visitasBase = VisitaCliente::query()
        ->entreFechas($fechaInicio, $fechaFin);

    $totalVisitas    = (int) $visitasBase->count();
    $visitasConVenta = (int) $visitasBase->clone()->conVenta()->count();
    $visitasSinVenta = $totalVisitas - $visitasConVenta;

    $tasaConversion  = $totalVisitas > 0 ? ($visitasConVenta / $totalVisitas) * 100 : 0;

    // Motivos (Top 5)
    $motivosNoVenta = $visitasBase->clone()
        ->sinVenta()
        ->whereNotNull('motivo_no_venta')
        ->select('motivo_no_venta', DB::raw('COUNT(*) as total'))
        ->groupBy('motivo_no_venta')
        ->orderByDesc('total')
        ->limit(5)
        ->get()
        ->map(function ($m) {
            $labels = [
                'sin_dinero'          => 'Sin dinero',
                'sin_stock_deseado'   => 'Sin stock',
                'precios_altos'       => 'Precios altos',
                'cliente_ausente'     => 'Ausente',
                'cliente_no_necesita' => 'No necesita',
                'otro'                => 'Otro',
            ];

            return [
                'motivo' => $labels[$m->motivo_no_venta] ?? $m->motivo_no_venta,
                'total'  => (int) $m->total,
            ];
        });

    /**
     * ⭐ NUEVO: Clientes sin venta (agrupado)
     * - nombre del cliente
     * - total de visitas sin venta
     * - última visita en el rango
     */
    $clientesSinVenta = $visitasBase->clone()
        ->sinVenta()
        ->with('cliente:id,nombre')
        ->select('cliente_id', DB::raw('COUNT(*) as total'), DB::raw('MAX(fecha_visita) as ultima_fecha'))
        ->groupBy('cliente_id')
        ->orderByDesc('total')
        ->limit(10) // puedes subirlo a 20 si quieres
        ->get()
        ->map(function ($row) {
            return [
                'cliente_id'   => $row->cliente_id,
                'cliente'      => $row->cliente->nombre ?? 'N/D',
                'total'        => (int) $row->total,
                'ultima_fecha' => $row->ultima_fecha ? Carbon::parse($row->ultima_fecha)->format('Y-m-d') : null,
            ];
        });

    /**
     * (Opcional pro) Últimas visitas sin venta (bitácora)
     * para ver “quién” y “cuándo” sin agrupar
     */
    $ultimasVisitasSinVenta = $visitasBase->clone()
        ->sinVenta()
        ->with(['cliente:id,nombre', 'user:id,name'])
        ->orderByDesc('fecha_visita')
        ->limit(10)
        ->get()
        ->map(function ($v) {
            return [
                'fecha'   => $v->fecha_visita ? $v->fecha_visita->format('Y-m-d') : null,
                'hora'    => $v->hora_visita_formateada,
                'cliente' => $v->cliente->nombre ?? 'N/D',
                'vendedor'=> $v->user->name ?? 'N/D',
                'motivo'  => $v->motivo_no_venta_legible ?? '—',
            ];
        });

    $visitasStats = [
        'total'                => $totalVisitas,
        'con_venta'            => $visitasConVenta,
        'sin_venta'            => $visitasSinVenta,
        'tasa_conversion'      => round($tasaConversion, 2),
        'motivos_no_venta'     => $motivosNoVenta,

        // ✅ NUEVO
        'clientes_sin_venta'   => $clientesSinVenta,
        'ultimas_sin_venta'    => $ultimasVisitasSinVenta,
    ];
}


        // ✅ INVENTARIO (tu tabla inventario_almacen)
        $unidadesTotalesInventario = (int) DB::table('inventario_almacen')
            ->where('cantidad', '>', 0)
            ->sum('cantidad');

        $productosConExistencia = (int) DB::table('inventario_almacen')
            ->where('cantidad', '>', 0)
            ->distinct()
            ->count('producto_id');

        $lotesAgotados = (int) DB::table('inventario_almacen')
            ->where('cantidad', '<=', 0)
            ->count();

        $valorInventario = (float) DB::table('inventario_almacen')
            ->join('productos', 'inventario_almacen.producto_id', '=', 'productos.id')
            ->where('productos.activo', true)
            ->sum(DB::raw('inventario_almacen.cantidad * productos.precio'));

        $inventarioPorAlmacen = DB::table('inventario_almacen')
            ->join('almacenes', 'inventario_almacen.almacen_id', '=', 'almacenes.id')
            ->select('almacenes.nombre', DB::raw('SUM(inventario_almacen.cantidad) as total_unidades'))
            ->groupBy('almacenes.id', 'almacenes.nombre')
            ->orderByDesc('total_unidades')
            ->get();

        $productosProximosCaducar = (int) DB::table('inventario_almacen')
            ->where('cantidad', '>', 0)
            ->whereNotNull('fecha_caducidad')
            ->whereDate('fecha_caducidad', '<=', now()->addDays(30))
            ->count();

        // ✅ Productos activos vs inactivos
        $productosActivos   = (int) Producto::where('activo', true)->count();
        $productosInactivos = (int) Producto::where('activo', false)->count();
        $productosTotal     = $productosActivos + $productosInactivos;

        // ✅ Top 10 próximos a caducar (con filtro caducanEn)
        $topCaducidad = DB::table('inventario_almacen')
            ->join('productos', 'inventario_almacen.producto_id', '=', 'productos.id')
            ->join('almacenes', 'inventario_almacen.almacen_id', '=', 'almacenes.id')
            ->where('inventario_almacen.cantidad', '>', 0)
            ->whereNotNull('inventario_almacen.fecha_caducidad')
            ->whereDate('inventario_almacen.fecha_caducidad', '<=', now()->addDays($caducanEn))
            ->orderBy('inventario_almacen.fecha_caducidad', 'asc')
            ->limit(10)
            ->get([
                'productos.id as producto_id',
                'productos.nombre as producto',
                'almacenes.id as almacen_id',
                'almacenes.nombre as almacen',
                'inventario_almacen.lote',
                'inventario_almacen.fecha_caducidad',
                'inventario_almacen.cantidad',
            ]);

        // 👥 CLIENTES
        $clientesTotal = (int) Cliente::where('activo', true)->count();
        $clientesConSaldo = (int) Cliente::whereHas('ventas', function ($q) {
            $q->whereIn('estado', ['credito', 'parcial']);
        })->count();

        return view('dashboard', compact(
            'fechaInicio',
            'fechaFin',
            'totalVentas',
            'cantidadVentas',
            'ticketPromedio',
            'ventasPagadas',
            'ventasCredito',
            'ventasParciales',
            'saldoPendienteTotal',
            'ventasHoy',
            'cantidadVentasHoy',
            'crecimiento',
            'topVendedores',
            'topClientes',
            'topProductos',
            'ventasPorCategoria',
            'ventasPorDia',
            'visitasStats',
            'unidadesTotalesInventario',
            'productosConExistencia',
            'lotesAgotados',
            'inventarioPorAlmacen',
            'valorInventario',
            'productosProximosCaducar',
            'productosActivos',
            'productosInactivos',
            'productosTotal',
            'topCaducidad',
            'caducanEn',
            'clientesTotal',
            'clientesConSaldo'
        ));
    }
}
