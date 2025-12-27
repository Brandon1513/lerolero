<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ClienteMovilController;
use App\Http\Controllers\Api\InventarioMovilController;
use App\Http\Controllers\Api\RechazoTemporalController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PromocionController;
use App\Http\Controllers\Api\VisitaClienteController;
use App\Http\Controllers\Api\CierreRutaMovilController;

/*
|--------------------------------------------------------------------------
| API Routes - Aplicación Móvil de Ventas
|--------------------------------------------------------------------------
*/

// ============================================
// 🔐 AUTENTICACIÓN (Sin middleware)
// ============================================
Route::post('/login', [AuthController::class, 'login']);

// ============================================
// 🔒 RUTAS PROTEGIDAS (auth:sanctum)
// ============================================
Route::middleware('auth:sanctum')->group(function () {
    
    // --------------------------------------------
    // 👤 USUARIO AUTENTICADO
    // --------------------------------------------
    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    Route::post('/update-password', function (Request $request) {
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

    // --------------------------------------------
    // 👥 CLIENTES
    // --------------------------------------------
    Route::prefix('clientes')->group(function () {
        // Lista completa de clientes asignados
        Route::get('/', [ClienteMovilController::class, 'index']);
        
        // Solo clientes del día actual
        Route::get('/dia', [ClienteMovilController::class, 'delDia']); // 👈 Cambié de clientes-dia a clientes/dia
        
        // Clientes asignados (alias)
        Route::get('/asignados', function (Request $request) {
            return $request->user()->clientes;
        });
        
        // Historial de ventas de un cliente
        Route::get('/{cliente}/ventas', [ClienteMovilController::class, 'ventas']);
        
        // Saldo de un cliente específico
        Route::get('/{cliente}/saldo', [ClienteMovilController::class, 'saldo']);
    });

    // Clientes con saldo pendiente
    Route::get('/clientes-con-saldo', [ClienteMovilController::class, 'indexConSaldo']);

    // --------------------------------------------
    // 📦 INVENTARIO
    // --------------------------------------------
    Route::get('/inventario', [InventarioMovilController::class, 'index']);

    // --------------------------------------------
    // 🛒 VENTAS
    // --------------------------------------------
    Route::prefix('venta')->group(function () {
        // Crear venta (contado / parcial / crédito)
        Route::post('/', [VentaController::class, 'store']);
        
        // Abonar a una venta existente
        Route::post('/{venta}/pagos', [VentaController::class, 'abonar']);
    });

    // --------------------------------------------
    // 🔄 RECHAZOS TEMPORALES (Cambios de venta)
    // --------------------------------------------
    Route::post('/rechazos', [RechazoTemporalController::class, 'store']);

    // --------------------------------------------
    // 🎁 PROMOCIONES
    // --------------------------------------------
    Route::get('/promociones', [PromocionController::class, 'index']);

    // --------------------------------------------
    // 🗺️ RUTAS Y VISITAS
    // --------------------------------------------
    
    // Solicitar cierre de ruta
    Route::post('/solicitar-cierre', [CierreRutaMovilController::class, 'solicitar']);
    
    // 📊 VISITAS A CLIENTES
    Route::prefix('visitas')->group(function () {
        // Registrar una visita
        Route::post('/', [VisitaClienteController::class, 'registrarVisita']);
        
        // Obtener visitas de hoy
        Route::get('/hoy', [VisitaClienteController::class, 'visitasHoy']);
        
        // Estadísticas de visitas
        Route::get('/estadisticas', [VisitaClienteController::class, 'estadisticas']);
        
        // Verificar si un cliente ya fue visitado hoy
        Route::get('/verificar/{cliente_id}', [VisitaClienteController::class, 'verificarVisita']);
        
        // Vincular una venta con su visita (uso interno)
        Route::post('/vincular/{venta_id}', [VisitaClienteController::class, 'vincularVenta']);
    });

    // --------------------------------------------
    // 🛠️ DEBUG (Opcional - remover en producción)
    // --------------------------------------------
    Route::get('/_debug/clientes', function (\Illuminate\Http\Request $request) {
        $user = $request->user();
        $diaActual = now()->locale('es')->isoFormat('dddd');
        $diaTitulo = ucfirst($diaActual);

        $q = \App\Models\Cliente::query()
            ->where('asignado_a', $user->id)
            ->with(['nivelPrecio:id,nombre'])
            ->orderBy('nombre');

        if (!$request->boolean('all')) {
            $q->whereJsonContains('dias_visita', $diaTitulo);
        }

        return $q->get(['id','nombre','telefono','latitud','longitud','nivel_precio_id']);
    });
});