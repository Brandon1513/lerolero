<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Promocion;
use Illuminate\Http\Request;

class PromocionController extends Controller
{
    public function index()
    {
        $promociones = Promocion::where('activo', true)
            ->with(['productos' => function ($query) {
                $query->select('productos.id', 'nombre')
                    ->withPivot('cantidad');
            }])
            ->get();

        return response()->json([
            'status' => true,
            'promociones' => $promociones
        ]);
    }
}
