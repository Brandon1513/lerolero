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

        // Solo los clientes asignados al vendedor actual
        $clientes = Cliente::where('asignado_a', $user->id)
                           ->with(['nivelPrecio']) // si tienes relación
                           ->get();

        return response()->json($clientes);
    }
}
