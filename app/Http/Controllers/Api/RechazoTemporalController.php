<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RechazoTemporal;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RechazoTemporalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'cambios' => 'required|array',
            'cambios.*.producto_id' => 'required|exists:productos,id',
            'cambios.*.cantidad' => 'required|integer|min:1',
            'cambios.*.motivo' => 'required|in:caducidad,no vendido,dañado,otro',
            'cambios.*.lote' => 'nullable|string|max:100',          // <- Nuevo campo
            'cambios.*.fecha_caducidad' => 'nullable|date',          // <- Nuevo campo
        ]);

        foreach ($request->cambios as $cambio) {
            RechazoTemporal::create([
                'producto_id' => $cambio['producto_id'],
                'vendedor_id' => Auth::id(),
                'cantidad' => $cambio['cantidad'],
                'motivo' => $cambio['motivo'],
                'lote' => $cambio['lote'] ?? null,                    // <- Ahora incluye el lote
                'fecha_caducidad' => $cambio['fecha_caducidad'] ?? null, // <- Ahora incluye la fecha de caducidad
                'fecha' => Carbon::now()->toDateString(),
            ]);
        }

        return response()->json(['message' => 'Cambios registrados correctamente.'], 201);
    }
}
