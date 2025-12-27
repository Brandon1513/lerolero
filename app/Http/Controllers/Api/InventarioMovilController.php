<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\ProductoNivelPrecio;

class InventarioMovilController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // 1) Almacén del vendedor
        $almacen = Almacen::where('user_id', $user->id)->first();
        if (!$almacen) {
            return response()->json(['message' => 'Almacén no asignado'], 404);
        }
        
        // 2) Nivel de precio del cliente (opcional)
        $clienteId = $request->query('cliente_id');
        $nivelId   = optional(Cliente::find($clienteId))->nivel_precio_id;
        
        // 3) Trae inventario por lote (con producto Y CATEGORÍA) ordenado FIFO por caducidad
        $inventario = Inventario::where('almacen_id', $almacen->id)
            ->where('cantidad', '>', 0)
            ->with([
                'producto' => function ($q) {
                    // Traemos producto con su categoría
                    $q->select('id', 'nombre', 'precio', 'imagen', 'categoria_id')
                      ->with('categoria:id,nombre'); // 🆕 Cargar categoría
                }
            ])
            ->orderBy('producto_id')
            ->orderBy('fecha_caducidad')
            ->get();
        
        // 4) Si hay nivel, precargamos TODOS los precios por nivel en UNA consulta
        $preciosPorNivel = collect();
        if ($nivelId) {
            $productoIds = $inventario->pluck('producto_id')->unique()->values();
            if ($productoIds->isNotEmpty()) {
                $preciosPorNivel = ProductoNivelPrecio::whereIn('producto_id', $productoIds)
                    ->where('nivel_precio_id', $nivelId)
                    ->pluck('precio', 'producto_id'); // [producto_id => precio]
            }
        }
        
        // 5) Armamos la respuesta sin N+1
        $payload = $inventario->map(function ($item) use ($nivelId, $preciosPorNivel) {
            $precioBase     = (float) ($item->producto->precio ?? 0);
            $precioCliente  = $nivelId ? optional($preciosPorNivel)[$item->producto_id] ?? null : null;
            
            return [
                'producto_id' => $item->producto_id,
                'producto' => [
                    'id'             => $item->producto->id,
                    'nombre'         => $item->producto->nombre,
                    'precio'         => $precioBase,
                    'precio_cliente' => $precioCliente ? (float)$precioCliente : null,
                    'imagen_url'     => $item->producto->imagen_url ?? $item->producto->imagen,
                    // 🆕 Agregar categoría al response
                    'categoria'      => $item->producto->categoria ? [
                        'id'     => $item->producto->categoria->id,
                        'nombre' => $item->producto->categoria->nombre,
                    ] : null,
                ],
                'lote'            => $item->lote,
                'fecha_caducidad' => $item->fecha_caducidad,
                'cantidad'        => $item->cantidad,
            ];
        })->values();
        
        return response()->json($payload);
    }
}