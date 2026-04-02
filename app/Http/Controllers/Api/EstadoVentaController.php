<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CierreRuta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EstadoVentaController extends Controller
{
    /**
     * GET /api/estado-venta
     *
     * Retorna si el vendedor puede vender o está bloqueado.
     * ✅ FIX: Auto-libera si el cierre pendiente es de un día anterior.
     */
    public function estado(Request $request)
    {
        $vendedor = $request->user();
        $hoy      = now()->toDateString();
        $debeLiberar = false;

        // ── ✅ AUTO-LIBERAR si el bloqueo es de un día anterior ──────────
        if (!empty($vendedor->ventas_bloqueadas)) {
            $cierreId = $vendedor->ventas_bloqueadas_cierre_id;

            if ($cierreId) {
                $cierre = CierreRuta::find($cierreId);

                if (!$cierre) {
                    $debeLiberar = true;
                    \Log::info("[EstadoVenta] cierre #{$cierreId} no existe → auto-liberar vendedor #{$vendedor->id}");
                } elseif (Carbon::parse($cierre->fecha)->toDateString() < $hoy) {
                    $debeLiberar = true;
                    \Log::info("[EstadoVenta] cierre #{$cierreId} es del " . Carbon::parse($cierre->fecha)->toDateString() . " → auto-liberar vendedor #{$vendedor->id}");
                }
            } else {
                $debeLiberar = true;
            }

            if ($debeLiberar) {
                $vendedor->update([
                    'ventas_bloqueadas'           => false,
                    'ventas_bloqueadas_desde'     => null,
                    'ventas_bloqueadas_motivo'    => null,
                    'ventas_bloqueadas_cierre_id' => null,
                ]);
                $vendedor->refresh();
            }
        } else {
            // ✅ Detectar si el admin liberó manualmente:
            // El vendedor ya no está bloqueado pero tiene un cierre de HOY ya liberado
            $cierreHoyLiberado = CierreRuta::where('vendedor_id', $vendedor->id)
                ->whereDate('fecha', $hoy)
                ->where('estatus', 'liberado')
                ->latest()
                ->first();

            // Si hay cierre liberado hoy, decirle a la app que fue liberado
            // para que limpie su cache de visitados
            if ($cierreHoyLiberado) {
                $debeLiberar = true;
            }
        }

        // ── Estado actual del cierre del día ─────────────────────────────
        $cierreHoy = CierreRuta::where('vendedor_id', $vendedor->id)
            ->whereDate('fecha', $hoy)
            ->latest()
            ->first();

        $bloqueado = (bool) $vendedor->ventas_bloqueadas;
        // fue_liberado = true cuando se auto-liberó ahora O cuando ya estaba libre (para que la app limpie su cache)
        $fueLiber = ($debeLiberar ?? false);

        return response()->json([
            'puede_vender'      => !$bloqueado,
            'ventas_bloqueadas' => $bloqueado,
            'fue_liberado'      => $fueLiber, // ✅ indica si fue liberado en esta llamada
            'motivo'            => $bloqueado ? $vendedor->ventas_bloqueadas_motivo : null,
            'desde'             => $bloqueado
                ? optional($vendedor->ventas_bloqueadas_desde)->toDateTimeString()
                : null,
            'cierre_hoy' => $cierreHoy ? [
                'id'      => $cierreHoy->id,
                'estatus' => $cierreHoy->estatus,
                'fecha'   => $cierreHoy->fecha,
            ] : null,
        ]);
    }
}