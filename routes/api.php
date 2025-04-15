<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteMovilController;
use App\Http\Controllers\Api\InventarioMovilController;
use App\Http\Controllers\Api\VentaController; // este sí está en Api
use App\Http\Controllers\Api\AuthController; // si lo tienes en esta ruta


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