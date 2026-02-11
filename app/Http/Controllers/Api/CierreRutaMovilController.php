<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\CierreRuta;
use App\Models\Inventario;
use App\Models\RechazoTemporal;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CierreRutaMovilController extends Controller
{
    public function solicitar(Request $request)
    {
        $vendedor = auth()->user();
        $hoy = now()->toDateString();

        try {
            $result = DB::transaction(function () use ($vendedor, $hoy) {

                // ✅ Evita duplicados (y ayuda contra doble tap)
                $yaExiste = CierreRuta::where('vendedor_id', $vendedor->id)
                    ->whereDate('fecha', $hoy)
                    ->where('estatus', 'pendiente') // ✅ solo impide si hay uno pendiente
                    ->lockForUpdate()
                    ->exists();

                if ($yaExiste) {
                    return [
                        'status' => 409,
                        'body'   => ['message' => 'Ya se ha enviado una solicitud de cierre hoy.'],
                    ];
                }

                // Obtener almacén del vendedor
                $almacenVendedor = Almacen::where('tipo', 'vendedor')
                    ->where('user_id', $vendedor->id)
                    ->first();

                if (!$almacenVendedor) {
                    return [
                        'status' => 404,
                        'body'   => ['message' => 'Almacén no encontrado.'],
                    ];
                }

                // 1) Inventario final (con lote y caducidad)
                $inventarioFinal = Inventario::where('almacen_id', $almacenVendedor->id)
                    ->get()
                    ->map(function ($item) {
                        return [
                            'producto_id'     => $item->producto_id,
                            'nombre'          => optional($item->producto)->nombre,
                            'cantidad'        => $item->cantidad,
                            'lote'            => $item->lote,
                            'fecha_caducidad' => $item->fecha_caducidad,
                        ];
                    })->toArray();

                // 2) Cambios desde rechazos (solo pendientes)
                $rechazos = RechazoTemporal::where('vendedor_id', $vendedor->id)
                    ->whereNull('venta_id') // ✅ recomendado
                    ->get();

                $cambios = $rechazos->map(function ($item) {
                    return [
                        'producto_id'     => $item->producto_id,
                        'nombre'          => optional($item->producto)->nombre,
                        'cantidad'        => $item->cantidad,
                        'motivo'          => $item->motivo,
                        'lote'            => $item->lote,
                        'fecha_caducidad' => $item->fecha_caducidad,
                    ];
                })->toArray();

                // 3) Ventas del día
                $ventas = Venta::where('vendedor_id', $vendedor->id)
                    ->whereDate('fecha', $hoy)
                    ->get();

                $total = (float) $ventas->sum('total');

                // 4) Crear cierre
                $cierre = CierreRuta::create([
                    'vendedor_id'         => $vendedor->id,
                    'fecha'               => $hoy,
                    'total_ventas'        => $total,
                    'inventario_inicial'  => [],
                    'inventario_final'    => $inventarioFinal,
                    'cambios'             => $cambios,
                    'estatus'             => 'pendiente',
                ]);

                // 5) Bloquear ventas del vendedor
                $vendedor->update([
                    'ventas_bloqueadas'           => true,
                    'ventas_bloqueadas_desde'     => now(),
                    'ventas_bloqueadas_motivo'    => 'Ruta finalizada (pendiente de liberación por administrador)',
                    'ventas_bloqueadas_cierre_id' => $cierre->id,
                ]);

                return [
                    'status' => 201,
                    'body'   => [
                        'message' => 'Solicitud enviada correctamente. Ventas bloqueadas hasta liberación.',
                        'cierre_id' => $cierre->id,
                        'ventas_bloqueadas' => true,
                    ],
                ];
            });

            return response()->json($result['body'], $result['status']);

        } catch (\Throwable $e) {
            \Log::error('Error al solicitar cierre de ruta', [
                'vendedor_id' => $vendedor->id,
                'fecha' => $hoy,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error al solicitar cierre. Intenta de nuevo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
