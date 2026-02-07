<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\ProductoNivelPrecio;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InventarioMovilController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1) Almacén del vendedor
        $almacen = Almacen::query()
            ->where('user_id', $user->id)
            ->when(\Schema::hasColumn('almacenes', 'tipo'), fn($q) => $q->where('tipo', 'vendedor'))
            ->first();

        if (!$almacen) {
            return response()->json(['message' => 'Almacén no asignado'], 404);
        }

        // 2) Nivel de precio del cliente (opcional)
        $clienteId = $request->query('cliente_id');
        $nivelId = $clienteId ? optional(Cliente::find($clienteId))->nivel_precio_id : null;

        // 3) Inventario por lote (FIFO) con producto + categoría
        $inventario = Inventario::query()
            ->where('almacen_id', $almacen->id)
            ->where('cantidad', '>', 0)
            ->with([
                'producto' => function ($q) {
                    $q->select('id', 'nombre', 'precio', 'imagen', 'categoria_id')
                      ->with('categoria:id,nombre');
                }
            ])
            ->orderBy('producto_id')
            ->orderBy('fecha_caducidad')
            ->get();

        // 4) Precios por nivel en 1 consulta
        $preciosPorNivel = collect();
        if ($nivelId) {
            $productoIds = $inventario->pluck('producto_id')->unique()->values();
            if ($productoIds->isNotEmpty()) {
                $preciosPorNivel = ProductoNivelPrecio::query()
                    ->whereIn('producto_id', $productoIds)
                    ->where('nivel_precio_id', $nivelId)
                    ->pluck('precio', 'producto_id');
            }
        }

        // helper: construir URL de imagen
        $toImageUrl = function ($path) {
            if (!$path) return null;
            // Si ya viene como URL completa o empieza con http, respétala
            if (Str::startsWith($path, ['http://', 'https://'])) return $path;
            // Si guardas en storage/public => Storage::url('productos/xxx.webp') => /storage/productos/xxx.webp
            return url(Storage::url($path));
        };

        // 5) Payload
        $payload = $inventario->map(function ($item) use ($nivelId, $preciosPorNivel, $toImageUrl) {
            $prod = $item->producto;

            $precioBase    = (float) ($prod->precio ?? 0);
            $precioCliente = $nivelId ? ($preciosPorNivel[$item->producto_id] ?? null) : null;

            return [
                'producto_id' => (int) $item->producto_id,
                'producto' => [
                    'id'             => (int) $prod->id,
                    'nombre'         => (string) $prod->nombre,
                    'precio'         => $precioBase,
                    'precio_cliente' => $precioCliente !== null ? (float) $precioCliente : null,

                    // ✅ Tu app usa imagen_url, aquí lo armamos desde "imagen"
                    'imagen_url'     => $toImageUrl($prod->imagen),

                    // ✅ Categoría lista para chips
                    'categoria'      => $prod->categoria ? [
                        'id'     => (int) $prod->categoria->id,
                        'nombre' => (string) $prod->categoria->nombre,
                    ] : null,
                ],
                'lote'            => $item->lote,
                'fecha_caducidad' => $item->fecha_caducidad ? (string) $item->fecha_caducidad : null,
                'cantidad'        => (float) $item->cantidad,
            ];
        })->values();

        return response()->json($payload);
    }
}
