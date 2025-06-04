<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Almacen;

class InventarioMovilController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Buscar el almacén del usuario autenticado
        $almacen = Almacen::where('user_id', $user->id)->first();

        if (!$almacen) {
            return response()->json(['message' => 'Almacén no asignado'], 404);
        }

        // Obtener el inventario por lote, con fecha de caducidad
        $inventario = Inventario::where('almacen_id', $almacen->id)
            ->where('cantidad', '>', 0)
            ->with('producto')
            ->orderBy('producto_id')
            ->orderBy('fecha_caducidad')
            ->get()
            ->map(function ($item) {
                return [
                    'producto_id' => $item->producto_id,
                    'producto' => [
                        'nombre' => $item->producto->nombre,
                        'precio' => $item->producto->precio,  // <-- Agregado
                        'imagen_url' => $item->producto->imagen_url,
                    ],
                    'lote' => $item->lote,
                    'fecha_caducidad' => $item->fecha_caducidad,
                    'cantidad' => $item->cantidad,
                ];
            });

        return response()->json($inventario);
    }
}
