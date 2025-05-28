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

        // Obtener el inventario de ese almacén con el producto relacionado
        $inventario = Inventario::where('almacen_id', $almacen->id)
        ->with('producto')
        ->get()
        ->map(function ($item) {
            $item->producto->makeHidden(['imagen']); // Oculta la ruta cruda si quieres
            $item->producto->imagen_url = $item->producto->imagen_url;
            return $item;
        });

    // 🔴 FALTABA ESTO
    return response()->json($inventario);
}
}