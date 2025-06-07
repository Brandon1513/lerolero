<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Venta;
use App\Models\Almacen;
use App\Models\CierreRuta;
use App\Models\Inventario;
use Illuminate\Http\Request;
use App\Models\RechazoTemporal;
use App\Http\Controllers\Controller;

class CierreRutaMovilController extends Controller
{
   public function solicitar(Request $request)
{
    $vendedor = auth()->user();
    $hoy = now()->toDateString();

    if (CierreRuta::where('vendedor_id', $vendedor->id)->whereDate('fecha', $hoy)->exists()) {
        return response()->json(['message' => 'Ya se ha enviado una solicitud de cierre hoy.'], 409);
    }

    // Obtener almacenes
    $almacenVendedor = Almacen::where('tipo', 'vendedor')->where('user_id', $vendedor->id)->first();
    if (!$almacenVendedor) {
        return response()->json(['message' => 'Almacén no encontrado.'], 404);
    }

    // 1. Inventario final desde su almacén (con lote y caducidad)
    $inventarioFinal = Inventario::where('almacen_id', $almacenVendedor->id)
        ->get()
        ->map(function ($item) {
            return [
                'producto_id' => $item->producto_id,
                'nombre' => optional($item->producto)->nombre,
                'cantidad' => $item->cantidad,
                'lote' => $item->lote,
                'fecha_caducidad' => $item->fecha_caducidad,
            ];
        })->toArray();

    // 2. Cambios desde tabla rechazos (con lote y caducidad)
    $rechazos = RechazoTemporal::where('vendedor_id', $vendedor->id)->get();
    $cambios = $rechazos->map(function ($item) {
        return [
            'producto_id' => $item->producto_id,
            'nombre' => optional($item->producto)->nombre,
            'cantidad' => $item->cantidad,
            'motivo' => $item->motivo,
            'lote' => $item->lote,
            'fecha_caducidad' => $item->fecha_caducidad,
        ];
    })->toArray();

    // 3. Ventas del día
    $ventas = Venta::where('vendedor_id', $vendedor->id)->whereDate('fecha', $hoy)->get();
    $total = $ventas->sum('total');

    // 4. Crear registro del cierre
    $cierre = CierreRuta::create([
        'vendedor_id' => $vendedor->id,
        'fecha' => $hoy,
        'total_ventas' => $total,
        'inventario_inicial' => [], // Esto lo llena el administrador en el cierre real
        'inventario_final' => $inventarioFinal,
        'cambios' => $cambios,
        'estatus' => 'pendiente',
    ]);

    return response()->json(['message' => 'Solicitud enviada correctamente.', 'cierre_id' => $cierre->id], 201);
}

}
