<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CierreRuta;
use App\Models\Venta;
use Carbon\Carbon;

class CierreRutaMovilController extends Controller
{
    public function solicitar(Request $request)
    {
        $vendedor = auth()->user();
        $hoy = now()->toDateString();

        // Verificar si ya existe un cierre para hoy
        $existe = CierreRuta::where('vendedor_id', $vendedor->id)
            ->whereDate('fecha', $hoy)
            ->first();

        if ($existe) {
            return response()->json([
                'message' => 'Ya se ha enviado una solicitud de cierre hoy.',
            ], 409);
        }

        // Calcular total de ventas del día
        $ventas = Venta::where('vendedor_id', $vendedor->id)
            ->whereDate('fecha', $hoy)
            ->get();

        $total = $ventas->sum('total');

        // Crear registro de cierre
        $cierre = CierreRuta::create([
            'vendedor_id' => $vendedor->id,
            'fecha' => $hoy,
            'total_ventas' => $total,
            'inventario_inicial' => [],
            'inventario_final' => [],
            'cambios' => [],
            'estatus' => 'pendiente',
        ]);

        return response()->json([
            'message' => 'Solicitud enviada correctamente.',
            'cierre_id' => $cierre->id,
        ], 201);
    }
}
