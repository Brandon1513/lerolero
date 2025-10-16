<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;

class ClienteMovilController extends Controller
{
     public function index(Request $request)
    {
        $user = $request->user();
        $diaActual = now()->locale('es')->isoFormat('dddd'); // "lunes"
        $diaTitulo = ucfirst($diaActual);                    // "Lunes"

        $q = Cliente::query()
            ->where('asignado_a', $user->id)
            ->with(['nivelPrecio:id,nombre'])
            ->orderBy('nombre');

        if (!$request->boolean('all')) {
            $q->whereJsonContains('dias_visita', $diaTitulo);
        }

        // Solo campos necesarios
        $clientes = $q->get(['id','nombre','telefono','latitud','longitud','nivel_precio_id']);

        $payload = $clientes->map(function ($c) {
            return [
                'id'        => $c->id,
                'nombre'    => $c->nombre,
                'telefono'  => $c->telefono,
                'latitud'   => $c->latitud,
                'longitud'  => $c->longitud,
                // 👇 clave exactamente como la app la espera
                'nivel_precio' => $c->nivelPrecio
                    ? ['id' => $c->nivelPrecio->id, 'nombre' => $c->nivelPrecio->nombre]
                    : null,
            ];
        })->values();

        return response()->json($payload);
    }
    public function delDia(Request $request)
    {
        $user = $request->user();
        $dia = ucfirst(now()->locale('es')->isoFormat('dddd')); // Ej. 'Lunes', 'Martes', etc.
    
        $clientes = Cliente::where('asignado_a', $user->id)
                    ->whereJsonContains('dias_visita', $dia)
                    ->get();
    
        return response()->json($clientes);
    }
    
    public function ventas($id)
    {
        $cliente = \App\Models\Cliente::findOrFail($id);

        $ventas = $cliente->ventas()
            ->with(['detalles.producto', 'rechazos.producto', 'cliente']) // 👈 cargamos también los productos devueltos y el cliente
            ->latest()
            ->get();

              // 👇 Mira qué trae exactamente
    dd($ventas->toArray());

        return response()->json($ventas);
    }


}
