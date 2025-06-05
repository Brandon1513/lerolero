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
        $diaActual = now()->locale('es')->isoFormat('dddd'); // Lunes, Martes, etc.

        $clientes = Cliente::where('asignado_a', $user->id)
                        ->whereJsonContains('dias_visita', ucfirst($diaActual))
                        ->with('nivelPrecio')
                        ->get();

        return response()->json($clientes);
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
