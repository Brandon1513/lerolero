<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;

class ClienteMovilController extends Controller
{
    /**
     * Lista de clientes asignados al vendedor.
     * - Si no viene ?all=1: solo los del día de visita.
     * - Incluye nivel de precio y saldo pendiente total (credito/parcial).
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $diaActual = now()->locale('es')->isoFormat('dddd'); // "lunes"
        $diaTitulo = ucfirst($diaActual);                    // "Lunes"

        // Subquery para sumar saldo por cliente (solo ventas con estado credito/parcial)
        $saldoSub = DB::table('ventas')
            ->selectRaw('cliente_id, SUM(saldo_pendiente) as saldo')
            ->whereIn('estado', ['credito', 'parcial'])
            ->groupBy('cliente_id');

        $q = Cliente::query()
            ->where('asignado_a', $user->id)
            ->leftJoinSub($saldoSub, 'v', function ($join) {
                $join->on('v.cliente_id', '=', 'clientes.id');
            })
            ->with(['nivelPrecio:id,nombre'])
            ->orderBy('clientes.nombre');

        if (!$request->boolean('all')) {
            $q->whereJsonContains('dias_visita', $diaTitulo);
        }

        // Solo campos necesarios + el saldo calculado
        $clientes = $q->get([
            'clientes.id',
            'clientes.nombre',
            'clientes.telefono',
            'clientes.latitud',
            'clientes.longitud',
            'clientes.nivel_precio_id',
            DB::raw('COALESCE(v.saldo,0) as saldo_pendiente_total'),
        ]);

        $payload = $clientes->map(function ($c) {
            return [
                'id'        => $c->id,
                'nombre'    => $c->nombre,
                'telefono'  => $c->telefono,
                'latitud'   => $c->latitud,
                'longitud'  => $c->longitud,
                'nivel_precio' => $c->nivelPrecio
                    ? ['id' => $c->nivelPrecio->id, 'nombre' => $c->nivelPrecio->nombre]
                    : null,

                // 👇 nuevo para la app
                'saldo_pendiente_total' => (float) $c->saldo_pendiente_total,
                'bloqueado'             => (float) $c->saldo_pendiente_total > 0,
            ];
        })->values();

        return response()->json($payload);
    }

    /**
     * Solo clientes del día (si quieres mantener este endpoint).
     */
    public function delDia(Request $request)
    {
        $user = $request->user();
        $dia = ucfirst(now()->locale('es')->isoFormat('dddd'));

        $clientes = Cliente::where('asignado_a', $user->id)
                    ->whereJsonContains('dias_visita', $dia)
                    ->get();

        return response()->json($clientes);
    }

    /**
     * Historial de ventas del cliente con relaciones.
     */
    public function ventas($id)
    {
        $cliente = Cliente::findOrFail($id);

        $ventas = $cliente->ventas()
            ->with(['detalles.producto', 'rechazos.producto', 'cliente'])
            ->latest()
            ->get();

        return response()->json($ventas);
    }
}
