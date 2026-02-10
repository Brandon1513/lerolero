<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class EstadoVentaController extends Controller
{
  public function estado()
  {
    $u = auth()->user();

    return response()->json([
      'ventas_bloqueadas' => (bool) $u->ventas_bloqueadas,
      'motivo' => $u->ventas_bloqueadas_motivo,
      'desde' => optional($u->ventas_bloqueadas_desde)->toDateTimeString(),
      'cierre_id' => $u->ventas_bloqueadas_cierre_id,
    ]);
  }
}
