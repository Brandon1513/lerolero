<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promocion;
use Illuminate\Http\Request;

class PromocionController extends Controller
{
    public function index(Request $request)
    {
        $hoy = now()->toDateString(); // zona horaria de la app

        $query = Promocion::query()
            ->where('activo', true)
            ->with(['productos' => function ($q) {
                // Lo suficiente para pintar en la app
                $q->select('productos.id', 'productos.nombre')
                  ->withPivot('cantidad');
            }])
            ->orderByDesc('id');

        // Filtrar por vigencia (si quieres que cuente la fecha)
        // Usa ?all=1 para saltarte la vigencia cuando quieras depurar.
        if (!$request->boolean('all')) {
            $query->whereDate('fecha_inicio', '<=', $hoy)
                  ->whereDate('fecha_fin', '>=', $hoy);
        }

        $promociones = $query->get();

        return response()->json([
            'status'       => true,
            'promociones'  => $promociones,
            // Campo útil de depuración: ver rápido si llegan vacías
            'count'        => $promociones->count(),
            'aplicoVigencia' => !$request->boolean('all'),
            'hoy'          => $hoy,
        ]);
    }
}
