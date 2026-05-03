<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CierreRuta;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EstadoVentaController extends Controller
{
    public function estado(Request $request)
    {
        $vendedor = $request->user();
        $hoy      = now()->toDateString();
        $debeLiberar = false;

        if (!empty($vendedor->ventas_bloqueadas)) {
            $cierreId = $vendedor->ventas_bloqueadas_cierre_id;
            if ($cierreId) {
                $cierre = CierreRuta::find($cierreId);
                if (!$cierre) {
                    $debeLiberar = true;
                } elseif (Carbon::parse($cierre->fecha)->toDateString() < $hoy) {
                    $debeLiberar = true;
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
            // ✅ Solo fue_liberado=true cuando el admin liberó MANUALMENTE (estatus=liberado)
            // Si el cierre fue 'cuadrado' normalmente, NO limpiar caché de ruta en la app
            $cierreHoyLiberado = CierreRuta::where('vendedor_id', $vendedor->id)
                ->whereDate('fecha', $hoy)
                ->where('estatus', 'liberado')
                ->latest()
                ->first();

            if ($cierreHoyLiberado) {
                $debeLiberar = true;
            }
        }

        $cierreHoy = CierreRuta::where('vendedor_id', $vendedor->id)
            ->whereDate('fecha', $hoy)
            ->latest()
            ->first();

        $bloqueado = (bool) $vendedor->ventas_bloqueadas;

        // ✅ Nuevo campo: indica que el cierre del día ya fue cuadrado correctamente
        // La app lo usa para mantener RUTA_CERRADA y no mostrar el botón de finalizar
        $cierreCuadradoHoy = $cierreHoy && $cierreHoy->estatus === 'cuadrado';

        return response()->json([
            'puede_vender'        => !$bloqueado,
            'ventas_bloqueadas'   => $bloqueado,
            'fue_liberado'        => $debeLiberar,
            'cierre_cuadrado_hoy' => $cierreCuadradoHoy,
            'motivo'              => $bloqueado ? $vendedor->ventas_bloqueadas_motivo : null,
            'desde'               => $bloqueado
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