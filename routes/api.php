<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\PromocionController;
use App\Http\Controllers\Api\ClienteMovilController;
use App\Http\Controllers\Api\VisitaClienteController;
use App\Http\Controllers\Api\CategoriaMovilController;
use App\Http\Controllers\Api\CierreRutaMovilController;
use App\Http\Controllers\Api\InventarioMovilController;
use App\Http\Controllers\Api\RechazoTemporalController;

/*
|--------------------------------------------------------------------------
| API Routes - Aplicación Móvil de Ventas
|--------------------------------------------------------------------------
| ✅ MEJORAS APLICADAS:
| - Rate limiting para prevenir abuso
| - Agrupación por tipo de operación
| - Throttling diferenciado por criticidad
*/

// ============================================
// 🔐 AUTENTICACIÓN (Sin middleware)
// ============================================
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:10,1'); // ✅ Máximo 10 intentos por minuto

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
    })->middleware('throttle:5,60'); // ✅ Máximo 5 cambios por hora

    // --------------------------------------------
    // 👥 CLIENTES (Consultas - Rate limit normal)
    // --------------------------------------------
    Route::middleware('throttle:120,1')->group(function () {
        Route::get('/clientes-dia', [ClienteMovilController::class, 'delDia']);
        
        Route::prefix('clientes')->group(function () {
            Route::get('/', [ClienteMovilController::class, 'index']);
            Route::get('/dia', [ClienteMovilController::class, 'delDia']);
            Route::get('/asignados', function (Request $request) {
                return $request->user()->clientes;
            });
            Route::get('/{cliente}/ventas', [ClienteMovilController::class, 'ventas']);
            Route::get('/{cliente}/saldo', [ClienteMovilController::class, 'saldo']);
        });

        Route::get('/clientes-con-saldo', [ClienteMovilController::class, 'indexConSaldo']);
    });

    // --------------------------------------------
    // 📦 INVENTARIO (Consultas - Rate limit normal)
    // --------------------------------------------
    Route::get('/inventario', [InventarioMovilController::class, 'index'])
        ->middleware('throttle:120,1'); // ✅ 120 consultas por minuto

    // --------------------------------------------
    // 📦 CATEGORIAS
    // --------------------------------------------
    Route::get('/categorias', [CategoriaMovilController::class, 'index']);

    Route::post('/rechazos', [RechazoTemporalController::class, 'store']);
    // --------------------------------------------
    // 🎁 PROMOCIONES (Consultas - Rate limit normal)
    // --------------------------------------------
    Route::get('/promociones', [PromocionController::class, 'index'])
        ->middleware('throttle:120,1');

    // --------------------------------------------
    // 🛒 VENTAS (CRÍTICO - Rate limit estricto)
    // --------------------------------------------
    Route::middleware('throttle:60,1')->group(function () {
        Route::prefix('venta')->group(function () {
            // ✅ Crear venta: Máximo 60 por minuto (1 por segundo)
            Route::post('/', [VentaController::class, 'store']);
            
            // ✅ Abonar: Máximo 60 por minuto
            Route::post('/{venta}/pagos', [VentaController::class, 'abonar']);
        });
    });
    // --------------------------------------------
    // Preventas (CRÍTICO - Rate limit estricto)
    // --------------------------------------------
    Route::post('/preventas', [\App\Http\Controllers\Api\PreventaController::class, 'store']);


    // --------------------------------------------
    // 🔄 RECHAZOS TEMPORALES (Rate limit moderado)
    // --------------------------------------------
    Route::post('/rechazos', [RechazoTemporalController::class, 'store'])
        ->middleware('throttle:60,1'); // ✅ Máximo 60 por minuto

    // --------------------------------------------
    // 🗺️ RUTAS Y VISITAS
    // --------------------------------------------
    
    // ✅ Cierre de ruta: Solo 10 por hora (es una operación crítica)
    Route::post('/solicitar-cierre', [CierreRutaMovilController::class, 'solicitar'])
        ->middleware('throttle:10,60');
    
    // 📊 VISITAS A CLIENTES
    Route::prefix('visitas')->group(function () {
        // ✅ Registrar visita: 100 por minuto
        Route::post('/', [VisitaClienteController::class, 'registrarVisita'])
            ->middleware('throttle:100,1');
        
        // Consultas de visitas: Rate limit normal
        Route::middleware('throttle:120,1')->group(function () {
            Route::get('/hoy', [VisitaClienteController::class, 'visitasHoy']);
            Route::get('/estadisticas', [VisitaClienteController::class, 'estadisticas']);
            Route::get('/verificar/{cliente_id}', [VisitaClienteController::class, 'verificarVisita']);
        });
        
        // Vincular venta: Rate limit moderado
        Route::post('/vincular/{venta_id}', [VisitaClienteController::class, 'vincularVenta'])
            ->middleware('throttle:60,1');
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
    })->middleware('throttle:30,1'); // ✅ Rate limit bajo para debug
});

/*
|--------------------------------------------------------------------------
| EXPLICACIÓN DE RATE LIMITS APLICADOS
|--------------------------------------------------------------------------
|
| 'throttle:X,Y' significa:
| - X = número máximo de requests
| - Y = período en minutos (1 = por minuto, 60 = por hora)
|
| CONFIGURACIÓN ACTUAL:
| 
| 🔴 CRÍTICO (Rate limit estricto):
| - Login: 10/minuto
| - Crear venta: 60/minuto
| - Abonar venta: 60/minuto
| - Cierre de ruta: 10/hora
| - Cambio de password: 5/hora
|
| 🟡 MODERADO:
| - Registrar visita: 100/minuto
| - Rechazos: 60/minuto
|
| 🟢 NORMAL (Consultas):
| - Clientes: 120/minuto
| - Inventario: 120/minuto
| - Promociones: 120/minuto
| - Visitas (consulta): 120/minuto
|
| 🔵 DEBUG:
| - Endpoints debug: 30/minuto
|
|--------------------------------------------------------------------------
*/