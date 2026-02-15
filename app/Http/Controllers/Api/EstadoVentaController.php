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

        // ── ✅ AUTO-LIBERAR si el bloqueo es de un día anterior ──────────
        if (!empty($vendedor->ventas_bloqueadas)) {
            $cierreId = $vendedor->ventas_bloqueadas_cierre_id;

            $debeLiberar = false;

            if ($cierreId) {
                $cierre = CierreRuta::find($cierreId);

                if (!$cierre) {
                    // El cierre fue eliminado → liberar
                    $debeLiberar = true;
                    \Log::info("[EstadoVenta] cierre #{$cierreId} no existe → auto-liberar vendedor #{$vendedor->id}");
                } elseif (Carbon::parse($cierre->fecha)->toDateString() < $hoy) {
                    // El cierre es de un día anterior → liberar
                    $debeLiberar = true;
                    \Log::info("[EstadoVenta] cierre #{$cierreId} es del " . Carbon::parse($cierre->fecha)->toDateString() . " → auto-liberar vendedor #{$vendedor->id}");
                }
            } else {
                // Bloqueado sin cierre_id → liberar (estado inválido)
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
        }

        // ── Estado actual del cierre del día ─────────────────────────────
        $cierreHoy = CierreRuta::where('vendedor_id', $vendedor->id)
            ->whereDate('fecha', $hoy)
            ->latest()
            ->first();

        $bloqueado = (bool) $vendedor->ventas_bloqueadas;

        return response()->json([
            'puede_vender'      => !$bloqueado,
            'ventas_bloqueadas' => $bloqueado,
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