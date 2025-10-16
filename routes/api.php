<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteMovilController;
use App\Http\Controllers\Api\InventarioMovilController;
use App\Http\Controllers\Api\RechazoTemporalController;
use App\Http\Controllers\Api\VentaController; // este sí está en Api
use App\Http\Controllers\Api\AuthController; // si lo tienes en esta ruta
use App\Http\Controllers\Api\PromocionController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/clientes', [ClienteMovilController::class, 'index']);
    Route::get('/inventario', [InventarioMovilController::class, 'index']);
    Route::post('/venta', [VentaController::class, 'store']);
});

Route::middleware('auth:sanctum')->get('/me', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->post('/update-password', function (Request $request) {
    $request->validate([
        'password' => 'required|min:6|confirmed',
    ]);

    $user = $request->user();
    $user->password = Hash::make($request->password);
    $user->save();

    // 🔥 Cerrar sesión actual
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Contraseña actualizada. Token eliminado.']);
});

//cliente asignado a vendedor

Route::middleware('auth:sanctum')->get('/clientes-asignados', function (Request $request) {
    return $request->user()->clientes; // Suponiendo que tienes la relación
});
Route::middleware('auth:sanctum')->get('/clientes-dia', [ClienteMovilController::class, 'delDia']);

Route::middleware('auth:sanctum')->get('/clientes/{cliente}/ventas', function (\App\Models\Cliente $cliente) {
    return $cliente->ventas()
        ->with(['cliente', 'detalles.producto', 'rechazos.producto']) // 👈 Incluimos también los rechazos
        ->latest()
        ->get();
});


Route::middleware('auth:sanctum')->post('/solicitar-cierre', [\App\Http\Controllers\Api\CierreRutaMovilController::class, 'solicitar']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/rechazos', [RechazoTemporalController::class, 'store']);
});

//Promociones
Route::middleware('auth:sanctum')->get('/promociones',[PromocionController::class, 'index']);


Route::middleware('auth:sanctum')->get('/_debug/clientes', function (\Illuminate\Http\Request $request) {
    $user = $request->user();

    // mismos filtros que usas en index(), pero sin mapear nada
    $diaActual = now()->locale('es')->isoFormat('dddd');
    $diaTitulo = ucfirst($diaActual);

    $q = \App\Models\Cliente::query()
        ->where('asignado_a', $user->id)
        ->with(['nivelPrecio:id,nombre'])
        ->orderBy('nombre');

    if (!$request->boolean('all')) {
        $q->whereJsonContains('dias_visita', $diaTitulo);
    }

    // devolvemos TAL CUAL lo que saca Eloquent
    return $q->get(['id','nombre','telefono','latitud','longitud','nivel_precio_id']);
});
