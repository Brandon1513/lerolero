<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoCatalogoController extends Controller
{
    /**
     * GET /api/productos-catalogo
     * Devuelve todos los productos del catálogo (sin lotes, sin stock).
     * Usado en CambiosModal para la lista de "productos devueltos".
     */
    public function index(Request $request)
    {
        $productos = Producto::query()
            ->with('categoria:id,nombre')
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'precio', 'categoria_id']);

        return response()->json(
            $productos->map(fn($p) => [
                'producto_id' => (int) $p->id,
                'producto' => [
                    'id'       => (int) $p->id,
                    'nombre'   => (string) $p->nombre,
                    'precio'   => (float) $p->precio,
                    'categoria' => $p->categoria ? [
                        'id'     => (int) $p->categoria->id,
                        'nombre' => (string) $p->categoria->nombre,
                    ] : null,
                ],
                'lote'            => null,
                'fecha_caducidad' => null,
                'cantidad'        => 0,
            ])->values()
        );
    }
}