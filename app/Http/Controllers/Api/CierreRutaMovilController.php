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
    $request->validate([
        'inventario_final' => 'required|array',
        'inventario_final.*.producto_id' => 'required|exists:productos,id',
        'inventario_final.*.cantidad' => 'required|numeric|min:0',
        'cambios' => 'nullable|array',
        'cambios.*.producto_id' => 'required|exists:productos,id',
        'cambios.*.cantidad' => 'required|numeric|min:0',
        'cambios.*.motivo' => 'required|string|in:caducidad,no vendido,dañado,otro',
    ]);

    $vendedor = auth()->user();

    $hoy = now()->toDateString();

    $existe = CierreRuta::where('vendedor_id', $vendedor->id)
        ->whereDate('fecha', $hoy)
        ->first();

    if ($existe) {
        return response()->json([
            'message' => 'Ya se ha enviado una solicitud de cierre hoy.',
        ], 409);
    }

    $ventas = \App\Models\Venta::where('vendedor_id', $vendedor->id)
        ->whereDate('fecha', $hoy)
        ->get();

    $total = $ventas->sum('total');

    $cierre = CierreRuta::create([
        'vendedor_id' => $vendedor->id,
        'fecha' => $hoy,
        'total_ventas' => $total,
        'inventario_inicial' => [],
        'inventario_final' => $request->inventario_final,
        'cambios' => $request->cambios ?? [],
        'estatus' => 'pendiente',
    ]);

    return response()->json([
        'message' => 'Solicitud enviada correctamente.',
        'cierre_id' => $cierre->id,
    ], 201);
}


}
