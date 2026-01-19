<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Traslado;
use App\Models\Inventario;
use Illuminate\Http\Request;
use App\Models\DetalleTraslado;
use Illuminate\Support\Facades\DB;

class TrasladoController extends Controller
{
public function index(Request $request)
    {
        $traslados = Traslado::with(['origen', 'destino'])
            ->when($request->filled('fecha_inicio'), fn($q) => $q->whereDate('fecha', '>=', $request->fecha_inicio))
            ->when($request->filled('fecha_fin'), fn($q) => $q->whereDate('fecha', '<=', $request->fecha_fin))
            ->when($request->filled('destino_id'), fn($q) => $q->where('almacen_destino_id', $request->destino_id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // ✅ bandera por fila
        $traslados->getCollection()->transform(function ($t) {
            $t->puede_eliminar = $this->canDeleteTraslado($t);
            return $t;
        });

        $almacenes = Almacen::where('activo', true)->get();

        return view('traslados.index', compact('traslados', 'almacenes'));
    }


    public function create()
    {
        $almacenes = Almacen::where('activo', true)->get();
        $productos = Producto::where('activo', true)->get();
        $inventario = Inventario::where('almacen_id', 1)->pluck('cantidad', 'producto_id');

        return view('traslados.create', compact('almacenes', 'productos', 'inventario'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'almacen_origen_id' => 'required|exists:almacenes,id|different:almacen_destino_id',
            'almacen_destino_id' => 'required|exists:almacenes,id',
            'fecha' => 'required|date',
            'detalles' => 'required|array',
            'detalles.*.*.cantidad' => 'nullable|numeric|min:1',
            'detalles.*.*.lote' => 'required|string',
            'detalles.*.*.fecha_caducidad' => 'required|date',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $traslado = Traslado::create([
                    'almacen_origen_id' => $request->almacen_origen_id,
                    'almacen_destino_id' => $request->almacen_destino_id,
                    'fecha' => $request->fecha,
                    'observaciones' => $request->observaciones,
                ]);

                foreach ($request->detalles as $productoId => $lotes) {
                    foreach ($lotes as $detalle) {
                        $cantidad = $detalle['cantidad'];
                        $lote = $detalle['lote'];
                        $caducidad = $detalle['fecha_caducidad'];

                        if (!$cantidad || $cantidad < 1) continue;

                        $loteOrigen = Inventario::where('almacen_id', $request->almacen_origen_id)
                            ->where('producto_id', $productoId)
                            ->where('lote', $lote)
                            ->where('fecha_caducidad', $caducidad)
                            ->first();

                        if (!$loteOrigen || $loteOrigen->cantidad < $cantidad) {
                            throw new \Exception("No hay suficiente stock del lote {$lote} para el producto ID {$productoId}.");
                        }

                        DetalleTraslado::create([
                            'traslado_id' => $traslado->id,
                            'producto_id' => $productoId,
                            'cantidad' => $cantidad,
                            'lote' => $lote,
                            'fecha_caducidad' => $caducidad,
                        ]);

                        $loteOrigen->decrement('cantidad', $cantidad);

                        $loteDestino = Inventario::firstOrNew([
                            'almacen_id' => $request->almacen_destino_id,
                            'producto_id' => $productoId,
                            'lote' => $lote,
                            'fecha_caducidad' => $caducidad,
                        ]);
                        $loteDestino->cantidad = ($loteDestino->cantidad ?? 0) + $cantidad;
                        $loteDestino->save();
                    }
                }
            });

            return redirect()->route('traslados.index')->with('success', 'Traslado registrado correctamente.');
        } catch (\Throwable $e) {
            \Log::error('Error en traslado', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(Traslado $traslado)
    {
        $traslado->load(['origen', 'destino', 'detalles.producto']);
        return view('traslados.show', compact('traslado'));
    }

    /**
     *  Eliminar traslado (solo si NO hay ventas y se puede revertir inventario)
     */
  public function destroy(Traslado $traslado)
{
    if (!$this->canDeleteTraslado($traslado)) {
        return redirect()->route('traslados.index')
            ->with('error', 'No se puede eliminar: ya hubo ventas o movimientos ese día, o es un traslado de rechazo.');
    }

    try {
        \DB::transaction(function () use ($traslado) {

            $traslado->load(['detalles']);

            foreach ($traslado->detalles as $d) {
                // Restar en destino
                $loteDestino = Inventario::where('almacen_id', $traslado->almacen_destino_id)
                    ->where('producto_id', $d->producto_id)
                    ->where('lote', $d->lote)
                    ->where('fecha_caducidad', $d->fecha_caducidad)
                    ->first();

                if (!$loteDestino || $loteDestino->cantidad < $d->cantidad) {
                    throw new \Exception("No se puede revertir inventario (Producto {$d->producto_id}, lote {$d->lote}).");
                }

                $loteDestino->decrement('cantidad', $d->cantidad);

                if ($loteDestino->fresh()->cantidad <= 0) {
                    $loteDestino->delete();
                }

                // Sumar en origen
                $loteOrigen = Inventario::firstOrNew([
                    'almacen_id' => $traslado->almacen_origen_id,
                    'producto_id' => $d->producto_id,
                    'lote' => $d->lote,
                    'fecha_caducidad' => $d->fecha_caducidad,
                ]);

                $loteOrigen->cantidad = ($loteOrigen->cantidad ?? 0) + $d->cantidad;
                $loteOrigen->save();
            }

            $traslado->detalles()->delete();
            $traslado->delete();
        });

        return redirect()->route('traslados.index')
            ->with('success', 'Traslado eliminado correctamente y el inventario fue revertido.');
    } catch (\Throwable $e) {
        \Log::error('Error al eliminar traslado', ['traslado_id' => $traslado->id, 'error' => $e->getMessage()]);
        return redirect()->route('traslados.index')
            ->with('error', 'No se pudo eliminar el traslado: ' . $e->getMessage());
    }
}



    /**
     *  True si el destino aún tiene todo para revertir (mismos lotes/caducidad/cantidad)
     */
    private function trasladoPuedeRevertirInventario(Traslado $traslado): bool
    {
        foreach ($traslado->detalles as $d) {
            $invDestino = Inventario::where('almacen_id', $traslado->almacen_destino_id)
                ->where('producto_id', $d->producto_id)
                ->where('lote', $d->lote)
                ->where('fecha_caducidad', $d->fecha_caducidad)
                ->first();

            if (!$invDestino || $invDestino->cantidad < $d->cantidad) {
                return false;
            }
        }
        return true;
    }

    /**
     * ✅ Detecta ventas. Si tu detalle_ventas NO tiene lote/caducidad, se usa regla conservadora.
     */
    private function trasladoTieneVentas(Traslado $traslado): bool
    {
        // 1) Intentar con detalle_ventas (si existe)
        $detalleTable = null;
        if (DB::getSchemaBuilder()->hasTable('detalle_ventas')) $detalleTable = 'detalle_ventas';
        if (!$detalleTable && DB::getSchemaBuilder()->hasTable('detalle_venta')) $detalleTable = 'detalle_venta';

        if ($detalleTable) {
            $productos = $traslado->detalles->pluck('producto_id')->unique()->values();

            $q = DB::table($detalleTable);

            // si tiene almacen_id (ideal)
            if (DB::getSchemaBuilder()->hasColumn($detalleTable, 'almacen_id')) {
                $q->where('almacen_id', $traslado->almacen_destino_id);
            }

            if (DB::getSchemaBuilder()->hasColumn($detalleTable, 'producto_id')) {
                $q->whereIn('producto_id', $productos);
            }

            // si se puede unir con ventas y filtrar por fecha
            if (DB::getSchemaBuilder()->hasColumn($detalleTable, 'venta_id') && DB::getSchemaBuilder()->hasTable('ventas')) {
                $q->join('ventas', 'ventas.id', '=', $detalleTable . '.venta_id');

                if (DB::getSchemaBuilder()->hasColumn('ventas', 'fecha')) {
                    $q->whereDate('ventas.fecha', '>=', $traslado->fecha);
                } elseif (DB::getSchemaBuilder()->hasColumn('ventas', 'created_at')) {
                    $q->whereDate('ventas.created_at', '>=', $traslado->fecha);
                }
            } else {
                // fallback por created_at del detalle
                if (DB::getSchemaBuilder()->hasColumn($detalleTable, 'created_at')) {
                    $q->whereDate($detalleTable . '.created_at', '>=', $traslado->fecha);
                }
            }

            return $q->exists();
        }

        // 2) Fallback conservador: si hay ventas del vendedor desde esa fecha, bloquear
        if (DB::getSchemaBuilder()->hasTable('ventas')) {
            $q = DB::table('ventas');

            // si ventas tiene almacen_id
            if (DB::getSchemaBuilder()->hasColumn('ventas', 'almacen_id')) {
                $q->where('almacen_id', $traslado->almacen_destino_id);
            }

            if (DB::getSchemaBuilder()->hasColumn('ventas', 'fecha')) {
                $q->whereDate('fecha', '>=', $traslado->fecha);
            } elseif (DB::getSchemaBuilder()->hasColumn('ventas', 'created_at')) {
                $q->whereDate('created_at', '>=', $traslado->fecha);
            }

            return $q->exists();
        }

        return false;
    }

    public function lotesPorAlmacen($almacenId)
    {
        $lotes = Inventario::with('producto')
            ->where('almacen_id', $almacenId)
            ->where('cantidad', '>', 0)
            ->whereNotNull('lote')
            ->orderBy('producto_id')
            ->orderBy('fecha_caducidad')
            ->get();

        $agrupado = [];

        foreach ($lotes as $lote) {
            $agrupado[$lote->producto_id][] = [
                'lote' => $lote->lote,
                'fecha_caducidad' => $lote->fecha_caducidad,
                'cantidad' => $lote->cantidad,
                'producto' => $lote->producto->nombre ?? 'Producto',
            ];
        }

        return response()->json($agrupado);
    }
    private function canDeleteTraslado(\App\Models\Traslado $traslado): bool
{
    $traslado->loadMissing(['origen', 'destino']);

    $fecha            = $traslado->fecha;
    $almacenVendedorId = $traslado->almacen_destino_id;

    // 1) NUNCA borrar traslados cuyo destino sea RECHAZO
    if (($traslado->destino->tipo ?? null) === 'rechazo') {
        return false;
    }

    // 2) Debe ser traslado de arranque de ruta: GENERAL -> VENDEDOR
    $esRuta = (($traslado->origen->tipo ?? null) === 'general')
           && (($traslado->destino->tipo ?? null) === 'vendedor');

    if (!$esRuta) {
        return false;
    }

    // 3) Si hubo ventas ese MISMO día desde el almacén del vendedor => NO eliminar
    $huboVentasEseDia = \DB::table('detalle_ventas')
        ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
        ->where('detalle_ventas.almacen_id', $almacenVendedorId)
        ->whereDate('ventas.fecha', $fecha)
        ->exists();

    if ($huboVentasEseDia) {
        return false;
    }

    // 4) Si hubo movimientos ese MISMO día saliendo del almacén del vendedor (ej. auto a Rechazo) => NO eliminar
    $huboMovimientosEseDia = \App\Models\Traslado::where('almacen_origen_id', $almacenVendedorId)
        ->whereDate('fecha', $fecha)
        ->exists();

    if ($huboMovimientosEseDia) {
        return false;
    }

    return true;
}

}
