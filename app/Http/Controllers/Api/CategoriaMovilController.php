<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Almacen;

class CategoriaMovilController extends Controller
{
    /**
     * GET /api/categorias
     * - categorías activas
     * - ?solo_con_inventario=1 => solo categorías con inventario > 0 en almacén del vendedor
     * - incluye métricas: productos_count, stock_total
     * - agrega "Todas" al inicio (con totales globales si aplica)
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Almacén del vendedor
        $almacen = Almacen::query()
            ->where('user_id', $user->id)
            ->where('tipo', 'vendedor')
            ->first();

        if (!$almacen) {
            return response()->json(['message' => 'Almacén no asignado'], 404);
        }

        $soloConInventario = $request->boolean('solo_con_inventario');

        // Base: categorias activas
        $q = DB::table('categorias')
            ->select(
                'categorias.id',
                'categorias.nombre'
            )
            ->where('categorias.activo', 1)
            ->orderBy('categorias.nombre');

        if ($soloConInventario) {
            // Con inventario: traemos métricas
            $q->join('productos', function ($join) {
                    $join->on('productos.categoria_id', '=', 'categorias.id')
                         ->where('productos.activo', 1);
                })
              ->join('inventario_almacen', function ($join) use ($almacen) {
                    $join->on('inventario_almacen.producto_id', '=', 'productos.id')
                         ->where('inventario_almacen.almacen_id', '=', $almacen->id)
                         ->where('inventario_almacen.cantidad', '>', 0);
                })
              ->groupBy('categorias.id', 'categorias.nombre')
              ->addSelect(DB::raw('COUNT(DISTINCT productos.id) as productos_count'))
              ->addSelect(DB::raw('SUM(inventario_almacen.cantidad) as stock_total'));
        } else {
            // Sin inventario: métricas en 0 (para UI consistente)
            $q->addSelect(DB::raw('0 as productos_count'))
              ->addSelect(DB::raw('0 as stock_total'));
        }

        $items = $q->get();

        // "Todas" (con totales si viene solo_con_inventario=1)
        $todas = [
            'id' => 0,
            'nombre' => 'Todas',
            'productos_count' => 0,
            'stock_total' => 0,
        ];

        if ($soloConInventario) {
            $todas['productos_count'] = (int) $items->sum(fn($c) => (int) $c->productos_count);
            $todas['stock_total']     = (float) $items->sum(fn($c) => (float) $c->stock_total);
        }

        $payload = collect([$todas])
            ->concat($items->map(function ($c) {
                return [
                    'id'             => (int) $c->id,
                    'nombre'         => (string) $c->nombre,
                    'productos_count'=> (int) ($c->productos_count ?? 0),
                    'stock_total'    => (float) ($c->stock_total ?? 0),
                ];
            }))
            ->values();

        return response()->json($payload);
    }
}

