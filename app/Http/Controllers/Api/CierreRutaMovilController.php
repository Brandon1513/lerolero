<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Almacen;
use App\Models\CierreRuta;
use App\Models\Inventario;
use App\Models\RechazoTemporal;
use App\Models\Venta;
use Carbon\Carbon;
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

                $yaExiste = CierreRuta::where('vendedor_id', $vendedor->id)
                    ->whereDate('fecha', $hoy)
                    ->where('estatus', 'pendiente')
                    ->lockForUpdate()
                    ->exists();

                if ($yaExiste) {
                    return [
                        'status' => 409,
                        'body'   => ['message' => 'Ya se ha enviado una solicitud de cierre hoy.'],
                    ];
                }

                $almacenVendedor = Almacen::where('tipo', 'vendedor')
                    ->where('user_id', $vendedor->id)
                    ->first();

                if (!$almacenVendedor) {
                    return [
                        'status' => 404,
                        'body'   => ['message' => 'Almacén no encontrado.'],
                    ];
                }

                // 1) Inventario final
                $inventarioFinal = Inventario::where('almacen_id', $almacenVendedor->id)
                    ->get()
                    ->map(fn($item) => [
                        'producto_id'     => $item->producto_id,
                        'nombre'          => optional($item->producto)->nombre,
                        'cantidad'        => $item->cantidad,
                        'lote'            => $item->lote,
                        'fecha_caducidad' => $item->fecha_caducidad,
                    ])->toArray();

                // 2) ✅ FIX: Cambios CON venta_id y sustituciones
                // Antes filtraba whereNull('venta_id') → perdía los cambios vinculados
                // Ahora incluye TODOS los rechazos del vendedor del día
                $rechazos = RechazoTemporal::where('vendedor_id', $vendedor->id)
                    ->whereNull('procesado_en') // ✅ solo pendientes
                    ->with(['producto', 'venta.cliente', 'detalles.producto'])
                    ->get();

                $cambios = $rechazos->map(fn($r) => [
                    'producto_id'     => $r->producto_id,
                    'nombre'          => optional($r->producto)->nombre,
                    'cantidad'        => $r->cantidad,
                    'motivo'          => $r->motivo,
                    'lote'            => $r->lote,
                    'fecha_caducidad' => $r->fecha_caducidad,
                    // ✅ Incluir venta_id para poder filtrar en historial
                    'venta_id'        => $r->venta_id,
                    'cliente_id'      => $r->venta?->cliente_id,
                    'cliente_nombre'  => $r->venta?->cliente?->nombre ?? 'Sin cliente',
                    // ✅ Incluir sustituciones del rechazo
                    'sustituciones'   => $r->detalles->map(fn($d) => [
                        'producto_id'     => $d->producto_id,
                        'nombre'          => optional($d->producto)->nombre,
                        'cantidad'        => $d->cantidad,
                        'lote'            => $d->lote,
                        'fecha_caducidad' => $d->fecha_caducidad,
                    ])->toArray(),
                ])->toArray();

                // 3) Ventas a incluir en este cierre
                // ✅ Incluye ventas de HOY + ventas sin cierre del día anterior
                // (el vendedor puede haber hecho ventas la noche anterior antes de cerrar ruta)
                $ultimoCierre = CierreRuta::where('vendedor_id', $vendedor->id)
                    ->where('estatus', '!=', 'pendiente') // solo cierres ya procesados
                    ->latest('id')
                    ->first();

                $ventasQuery = Venta::where('vendedor_id', $vendedor->id);

                if ($ultimoCierre) {
                    // Tomar ventas desde después del último cierre procesado hasta ahora
                    $ventasQuery->where('created_at', '>', $ultimoCierre->created_at);
                } else {
                    // Sin cierres previos — solo las de hoy
                    $ventasQuery->whereDate('fecha', $hoy);
                }

                $ventas = $ventasQuery->get();
                $total = (float) $ventas->sum('total');

                // Buscar si ya existe un cierre anterior del mismo día
                $cierreAnterior = CierreRuta::where('vendedor_id', $vendedor->id)
                    ->whereDate('fecha', $hoy)
                    ->where('id', '!=', 0)
                    ->latest('id')
                    ->first();

                // ✅ Fecha desde: el día después del último cierre, o la fecha de la venta más antigua
                $fechaDesde = $ultimoCierre
                    ? Carbon::parse($ultimoCierre->created_at)->addSecond()->toDateTimeString()
                    : ($ventas->min('created_at') ?? $hoy);

                // 4) Crear cierre
                $cierre = CierreRuta::create([
                    'vendedor_id'        => $vendedor->id,
                    'fecha'              => $hoy,
                    'fecha_desde'        => $fechaDesde,
                    'total_ventas'       => $total,
                    'inventario_inicial' => [],
                    'inventario_final'   => $inventarioFinal,
                    'cambios'            => $cambios,
                    'estatus'            => 'pendiente',
                    'cierre_anterior_id' => $cierreAnterior?->id,
                ]);

                // 5) Bloquear vendedor
                $vendedor->update([
                    'ventas_bloqueadas'           => true,
                    'ventas_bloqueadas_desde'     => now(),
                    'ventas_bloqueadas_motivo'    => 'Ruta finalizada (pendiente de liberación por administrador)',
                    'ventas_bloqueadas_cierre_id' => $cierre->id,
                ]);

                return [
                    'status' => 201,
                    'body'   => [
                        'message'           => 'Solicitud enviada correctamente. Ventas bloqueadas hasta liberación.',
                        'cierre_id'         => $cierre->id,
                        'ventas_bloqueadas' => true,
                    ],
                ];
            });

            return response()->json($result['body'], $result['status']);

        } catch (\Throwable $e) {
            \Log::error('Error al solicitar cierre de ruta', [
                'vendedor_id' => $vendedor->id,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error al solicitar cierre. Intenta de nuevo.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}