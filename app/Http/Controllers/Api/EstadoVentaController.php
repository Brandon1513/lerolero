<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class EstadoVentaController extends Controller
{
  /**
   * Verificar estado de bloqueo de ventas del vendedor
   * 
   * Retorna si el vendedor tiene ventas bloqueadas y si fue liberado
   */
  public function estado()
  {
    $u = auth()->user();
    
    // ✅ Si ventas_bloqueadas es false, significa que fue liberado
    $bloqueado = (bool) $u->ventas_bloqueadas;
    
    return response()->json([
      'ventas_bloqueadas' => $bloqueado,
      'motivo' => $u->ventas_bloqueadas_motivo,
      'desde' => optional($u->ventas_bloqueadas_desde)->toDateTimeString(),
      'cierre_id' => $u->ventas_bloqueadas_cierre_id,
      'fue_liberado' => !$bloqueado, // 👈 Nuevo: true si fue liberado
    ]);
  }
}