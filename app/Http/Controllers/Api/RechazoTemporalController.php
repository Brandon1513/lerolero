<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Almacen;
use App\Models\Inventario; // tu modelo para inventario_almacen
use Illuminate\Http\Request;
use App\Models\RechazoTemporal;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\RechazoTemporalDetalle;

class RechazoTemporalController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'cambios' => 'required|array|min:1',

        // devuelto
        'cambios.*.producto_id'     => 'required|exists:productos,id',
        'cambios.*.cantidad'        => 'required|numeric|min:1',
        'cambios.*.motivo'          => 'required|in:caducidad,no vendido,dañado,otro',
        'cambios.*.lote'            => 'nullable|string|max:255',
        'cambios.*.fecha_caducidad' => 'nullable|date',

        // entregado (sustituciones)
        'cambios.*.sustituciones' => 'required|array|min:1',
        'cambios.*.sustituciones.*.producto_id'     => 'required|exists:productos,id',
        'cambios.*.sustituciones.*.cantidad'        => 'required|numeric|min:0.01',
        'cambios.*.sustituciones.*.lote'            => 'nullable|string|max:255',
        'cambios.*.sustituciones.*.fecha_caducidad' => 'nullable|date',
    ]);

    $userId = Auth::id();

    $almacenVendedor = Almacen::where('user_id', $userId)->firstOrFail();
    $almacenRechazo  = $this->resolveAlmacenRechazo();

    $idsCreados = [];

    DB::transaction(function () use ($request, $userId, $almacenVendedor, $almacenRechazo, &$idsCreados) {

            foreach ($request->cambios as $cambio) {

                $qtyDevuelta = (float) $cambio['cantidad'];

                // ✅ validar suma sustituciones
                $sumaEntregada = collect($cambio['sustituciones'])
                    ->sum(fn ($s) => (float) $s['cantidad']);

                if ($sumaEntregada <= 0) {
                    abort(422, 'Debes capturar al menos una sustitución.');
                }

                // ✅ EXACTO: entregado debe ser igual a devuelto
                if (abs($sumaEntregada - $qtyDevuelta) > 0.0001) {
                    abort(422, "La cantidad entregada ($sumaEntregada) debe ser exactamente igual a la devuelta ($qtyDevuelta).");
                }

            // 1) crear cabecera (devuelto)
            $rechazo = RechazoTemporal::create([
                'producto_id'     => $cambio['producto_id'],
                'vendedor_id'     => $userId,
                'cantidad'        => $qtyDevuelta,
                'motivo'          => $cambio['motivo'],
                'lote'            => $cambio['lote'] ?? null,
                'fecha_caducidad' => $cambio['fecha_caducidad'] ?? null,
                'fecha'           => Carbon::now()->toDateString(),
                'almacen_id'      => $almacenVendedor->id,
                'venta_id'        => null,
            ]);

            $idsCreados[] = $rechazo->id;

            // 2) DEVUELTO -> sumar al almacén rechazo
            $this->incrementarInventario(
                $almacenRechazo->id,
                (int) $cambio['producto_id'],
                $cambio['lote'] ?? null,
                $cambio['fecha_caducidad'] ?? null,
                $qtyDevuelta
            );

            // 3) ENTREGADO -> validar y descontar del almacén vendedor + guardar detalle
            foreach ($cambio['sustituciones'] as $s) {

                $prodEnt = (int) $s['producto_id'];
                $qtyEnt  = (float) $s['cantidad'];
                $loteEnt = $s['lote'] ?? null;
                $cadEnt  = $s['fecha_caducidad'] ?? null;

                $inv = $this->getInventarioForUpdate($almacenVendedor->id, $prodEnt, $loteEnt, $cadEnt);

                $disp = $inv ? (float) $inv->cantidad : 0.0;
                if ($disp + 0.0001 < $qtyEnt) {
                    abort(422, "Stock insuficiente producto_id={$prodEnt}. Disponible: {$disp}, requerido: {$qtyEnt}.");
                }

                RechazoTemporalDetalle::create([
                    'rechazo_temporal_id' => $rechazo->id,
                    'producto_id'         => $prodEnt,
                    'cantidad'            => $qtyEnt,
                    'lote'                => $loteEnt,
                    'fecha_caducidad'     => $cadEnt,
                    'almacen_id'          => $almacenVendedor->id,
                ]);

                $inv->cantidad = (float) $inv->cantidad - $qtyEnt;
                $inv->save();
            }
        }
    });

    // ✅ regresar rechazos recién creados con nombres
    $rechazos = RechazoTemporal::with([
            'producto:id,nombre',
            'detalles.producto:id,nombre',
        ])
        ->whereIn('id', $idsCreados)
        ->get();

    return response()->json([
    'message'      => 'Cambios registrados correctamente.',
    'rechazos_ids' => $idsCreados,      // ✅ ahora el front lo lee directo
    'rechazos'     => $rechazos,
    ], 201);

}


    private function resolveAlmacenRechazo(): Almacen
    {
        // por tipo
        $a = Almacen::where('tipo', 'rechazo')->first();
        if ($a) return $a;

        // por nombre
        $a = Almacen::where('nombre', 'like', '%rechazo%')
            ->orWhere('nombre', 'like', '%cambio%')
            ->first();
        if ($a) return $a;

        abort(500, 'No está configurado el almacén de rechazo (tipo="rechazo").');
    }

    private function getInventarioForUpdate(int $almacenId, int $productoId, ?string $lote, ?string $fechaCaducidad)
    {
        $q = Inventario::where('almacen_id', $almacenId)
            ->where('producto_id', $productoId);

        $lote === null ? $q->whereNull('lote') : $q->where('lote', $lote);
        $fechaCaducidad === null ? $q->whereNull('fecha_caducidad') : $q->where('fecha_caducidad', $fechaCaducidad);

        return $q->lockForUpdate()->first();
    }

    private function incrementarInventario(int $almacenId, int $productoId, ?string $lote, ?string $fechaCaducidad, float $cantidad): void
    {
        $q = Inventario::where('almacen_id', $almacenId)
            ->where('producto_id', $productoId);

        $lote === null ? $q->whereNull('lote') : $q->where('lote', $lote);
        $fechaCaducidad === null ? $q->whereNull('fecha_caducidad') : $q->where('fecha_caducidad', $fechaCaducidad);

        $row = $q->lockForUpdate()->first();

        if ($row) {
            $row->cantidad = (float) $row->cantidad + $cantidad;
            $row->save();
        } else {
            Inventario::create([
                'almacen_id'      => $almacenId,
                'producto_id'     => $productoId,
                'cantidad'        => $cantidad,
                'lote'            => $lote,
                'fecha_caducidad' => $fechaCaducidad,
            ]);
        }
    }
}
