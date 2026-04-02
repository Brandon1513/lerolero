<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index(Request $request)
    {
        $hoy = now()->toDateString();

        // ── Stats globales (siempre, sin filtros) ──────────────
        $statsTotalUnidades      = Inventario::sum('cantidad');
        $statsProductosDistintos = Inventario::where('cantidad', '>', 0)
                                       ->distinct('producto_id')->count('producto_id');
        $statsProximosVencer     = Inventario::where('cantidad', '>', 0)
                                       ->whereNotNull('fecha_caducidad')
                                       ->whereDate('fecha_caducidad', '>=', $hoy)
                                       ->whereDate('fecha_caducidad', '<=', now()->addDays(30)->toDateString())
                                       ->count();
        $statsVencidos           = Inventario::where('cantidad', '>', 0)
                                       ->whereNotNull('fecha_caducidad')
                                       ->whereDate('fecha_caducidad', '<', $hoy)
                                       ->count();

        // ── Query filtrada ─────────────────────────────────────
        $query = Inventario::with(['producto.categoria', 'almacen']);

        // Filtro: stock (por defecto solo con_stock)
        $stock = $request->input('stock', 'con_stock');
        match ($stock) {
            'con_stock' => $query->where('cantidad', '>', 0),
            'sin_stock' => $query->where('cantidad', 0),
            default     => null,  // 'todos' → sin restricción
        };

        // Filtro: búsqueda por nombre de producto
        if ($request->filled('buscar')) {
            $query->whereHas('producto', fn($q) =>
                $q->where('nombre', 'like', '%'.$request->buscar.'%')
            );
        }

        // Filtro: almacén
        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }

        // Filtro: caducidad
        if ($request->filled('caducidad')) {
            match ($request->caducidad) {
                'vencido' => $query->whereNotNull('fecha_caducidad')
                                   ->whereDate('fecha_caducidad', '<', $hoy),
                'pronto'  => $query->whereNotNull('fecha_caducidad')
                                   ->whereDate('fecha_caducidad', '>=', $hoy)
                                   ->whereDate('fecha_caducidad', '<=', now()->addDays(30)->toDateString()),
                'vigente' => $query->where(fn($q) =>
                                 $q->whereNull('fecha_caducidad')
                                   ->orWhereDate('fecha_caducidad', '>', now()->addDays(30)->toDateString())
                             ),
                default   => null,
            };
        }

        $inventarios = $query->clone()
            ->orderBy('fecha_caducidad')
            ->paginate(15)
            ->withQueryString();

        // ✅ Todos los registros para el PDF (sin paginar)
        $inventariosTodos = $query
            ->orderBy('fecha_caducidad')
            ->get();

        $almacenes = Almacen::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('inventario.index', compact(
            'inventarios',
            'inventariosTodos',
            'almacenes',
            'productos',
            'statsTotalUnidades',
            'statsProductosDistintos',
            'statsProximosVencer',
            'statsVencidos'
        ));
    }
}