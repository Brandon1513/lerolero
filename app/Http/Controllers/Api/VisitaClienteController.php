<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VisitaCliente;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class VisitaClienteController extends Controller
{
    /**
     * Registrar una visita a cliente
     */
    public function registrarVisita(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'realizo_venta' => 'required|boolean',
            'venta_id' => 'nullable|exists:ventas,id',
            'motivo_no_venta' => 'required_if:realizo_venta,false|nullable|in:sin_dinero,sin_stock_deseado,precios_altos,cliente_ausente,cliente_no_necesita,otro',
            'observaciones' => 'nullable|string|max:500',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // ✅ Verificar si ya existe una visita para este cliente HOY
            $hoy = now()->toDateString();
            $visitaExistente = VisitaCliente::where('user_id', auth()->id())
                ->where('cliente_id', $request->cliente_id)
                ->whereDate('fecha_visita', $hoy)
                ->first();

            if ($visitaExistente) {
                // 🔄 Actualizar la visita existente en lugar de crear una nueva
                $visitaExistente->update([
                    'hora_visita' => now()->toTimeString(),
                    'realizo_venta' => $request->realizo_venta,
                    'venta_id' => $request->venta_id,
                    'motivo_no_venta' => $request->motivo_no_venta,
                    'observaciones' => $request->observaciones,
                    'latitud' => $request->latitud,
                    'longitud' => $request->longitud,
                    'estado' => 'visitado',
                ]);

                return response()->json([
                    'message' => 'Visita actualizada exitosamente',
                    'visita' => $visitaExistente->load('cliente')
                ], 200);
            }

            // 🆕 Crear nueva visita
            $visita = VisitaCliente::create([
                'user_id' => auth()->id(),
                'cliente_id' => $request->cliente_id,
                'fecha_visita' => $hoy,
                'hora_visita' => now()->toTimeString(),
                'realizo_venta' => $request->realizo_venta,
                'venta_id' => $request->venta_id,
                'motivo_no_venta' => $request->motivo_no_venta,
                'observaciones' => $request->observaciones,
                'latitud' => $request->latitud,
                'longitud' => $request->longitud,
                'estado' => 'visitado',
            ]);

            return response()->json([
                'message' => 'Visita registrada exitosamente',
                'visita' => $visita->load('cliente')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al registrar la visita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener visitas del día actual
     */
    public function visitasHoy()
    {
        $visitas = VisitaCliente::with(['cliente', 'venta'])
            ->where('user_id', auth()->id())
            ->whereDate('fecha_visita', now()->toDateString())
            ->orderBy('hora_visita', 'desc')
            ->get();

        $estadisticas = [
            'total_visitas' => $visitas->count(),
            'con_venta' => $visitas->where('realizo_venta', true)->count(),
            'sin_venta' => $visitas->where('realizo_venta', false)->count(),
            'tasa_conversion' => $visitas->count() > 0 
                ? round(($visitas->where('realizo_venta', true)->count() / $visitas->count()) * 100, 2) 
                : 0
        ];

        return response()->json([
            'visitas' => $visitas,
            'estadisticas' => $estadisticas
        ]);
    }

    /**
     * Obtener estadísticas de visitas por período
     */
    public function estadisticas(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->toDateString());
        $fechaFin = $request->input('fecha_fin', now()->toDateString());

        $visitas = VisitaCliente::where('user_id', auth()->id())
            ->whereBetween('fecha_visita', [$fechaInicio, $fechaFin])
            ->get();

        // Agrupar motivos de no venta
        $motivosNoVenta = $visitas
            ->where('realizo_venta', false)
            ->whereNotNull('motivo_no_venta')
            ->groupBy('motivo_no_venta')
            ->map(fn($grupo) => $grupo->count());

        // 🆕 Clientes más visitados sin venta
        $clientesSinVenta = $visitas
            ->where('realizo_venta', false)
            ->groupBy('cliente_id')
            ->map(fn($grupo) => [
                'cliente_id' => $grupo->first()->cliente_id,
                'cliente_nombre' => $grupo->first()->cliente->nombre ?? 'N/D',
                'total_visitas_sin_venta' => $grupo->count()
            ])
            ->sortByDesc('total_visitas_sin_venta')
            ->take(10)
            ->values();

        return response()->json([
            'total_visitas' => $visitas->count(),
            'con_venta' => $visitas->where('realizo_venta', true)->count(),
            'sin_venta' => $visitas->where('realizo_venta', false)->count(),
            'tasa_conversion' => $visitas->count() > 0 
                ? round(($visitas->where('realizo_venta', true)->count() / $visitas->count()) * 100, 2) 
                : 0,
            'motivos_no_venta' => $motivosNoVenta,
            'clientes_dificiles' => $clientesSinVenta,
            'periodo' => [
                'desde' => $fechaInicio,
                'hasta' => $fechaFin
            ]
        ]);
    }

    /**
     * Verificar si un cliente ya fue visitado hoy
     */
    public function verificarVisita($clienteId)
    {
        $visita = VisitaCliente::where('user_id', auth()->id())
            ->whereDate('fecha_visita', now()->toDateString())
            ->where('cliente_id', $clienteId)
            ->first();

        return response()->json([
            'ya_visitado' => $visita !== null,
            'visita' => $visita
        ]);
    }

    /**
     * 🆕 Vincular automáticamente una venta con su visita
     * (Llamar desde VentaController después de crear la venta)
     */
    public function vincularVenta($ventaId)
    {
        try {
            $venta = Venta::findOrFail($ventaId);
            $hoy = now()->toDateString();

            // Buscar si existe una visita sin venta_id para este cliente hoy
            $visita = VisitaCliente::where('user_id', auth()->id())
                ->where('cliente_id', $venta->cliente_id)
                ->whereDate('fecha_visita', $hoy)
                ->whereNull('venta_id')
                ->first();

            if ($visita) {
                $visita->update([
                    'venta_id' => $venta->id,
                    'realizo_venta' => true,
                    'motivo_no_venta' => null,
                ]);

                return response()->json([
                    'message' => 'Venta vinculada a visita exitosamente',
                    'visita' => $visita
                ]);
            }

            return response()->json([
                'message' => 'No se encontró visita para vincular'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al vincular venta',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}