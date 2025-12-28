<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\NivelPrecioController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\DashboardController; // 👈 NUEVO

Route::get('/', function () {
    return view('welcome');
});

// ========================================
// 📊 DASHBOARD - RUTA PRINCIPAL
// ========================================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ========================================
// 👤 PERFIL DE USUARIO
// ========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================================
// 🔒 RUTAS DE ADMINISTRADOR
// ========================================
Route::middleware(['auth', 'role:administrador'])->group(function () {
    
    // 👥 CLIENTES
    Route::resource('clientes', ClienteController::class);
    Route::patch('/clientes/{cliente}/toggle', [ClienteController::class, 'toggleActivo'])->name('clientes.toggle');
    
    // 📦 PRODUCTOS
    Route::resource('productos', ProductoController::class)->parameters([
        'productos' => 'producto'
    ]);
    Route::patch('productos/{producto}/toggle', [ProductoController::class, 'toggle'])->name('productos.toggle');
    
    // 📋 INVENTARIO
    Route::resource('inventarios', InventarioController::class);
    Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario/almacen/{id}', [InventarioController::class, 'porAlmacen'])->name('inventario.por_almacen');
    
    // 👨‍💼 VENDEDORES
    Route::resource('vendedores', VendedorController::class)->parameters([
        'vendedores' => 'vendedor'
    ]);
    Route::patch('vendedores/{vendedor}/toggle', [VendedorController::class, 'toggleEstado'])->name('vendedores.toggle');
    
    // 📂 CATEGORÍAS
    Route::resource('categorias', CategoriaController::class);
    Route::patch('/categorias/{categoria}/toggle', [CategoriaController::class, 'toggle'])->name('categorias.toggle');
    
    // 📏 UNIDADES DE MEDIDA
    Route::resource('unidades', UnidadMedidaController::class)->parameters([
        'unidades' => 'unidad'
    ]);
    Route::patch('unidades/{unidad}/toggle', [UnidadMedidaController::class, 'toggle'])->name('unidades.toggle');
    
    // 💰 NIVELES DE PRECIO
    Route::resource('niveles-precio', NivelPrecioController::class);
    
    // 🏢 ALMACENES
    Route::resource('almacenes', AlmacenController::class);
    Route::patch('/almacenes/{almacen}/toggle', [AlmacenController::class, 'toggleActivo'])->name('almacenes.toggle');
    
    // 🔄 TRASLADOS
    Route::resource('traslados', App\Http\Controllers\TrasladoController::class);
    Route::get('/traslados/{traslado}', [App\Http\Controllers\TrasladoController::class, 'show'])->name('traslados.show');
    Route::get('/traslados/lotes/{almacen}', [App\Http\Controllers\TrasladoController::class, 'lotesPorAlmacen']);
    
    // 💸 VENTAS
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store']);
    Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
    Route::get('/panel-ventas', [VentaController::class, 'panel'])->name('ventas.panel');
    
    // 🎁 PROMOCIONES
    Route::resource('promociones', PromocionController::class)->parameters([
        'promociones' => 'promocion'
    ]);
    Route::patch('/promociones/{promocion}/toggle', [PromocionController::class, 'toggle'])->name('promociones.toggle');
    
    // 🏭 PRODUCCIONES
    Route::resource('producciones', App\Http\Controllers\ProduccionController::class)
    ->only(['index', 'create', 'store', 'show', 'destroy'])
    ->parameters(['producciones' => 'produccion']);

    
    // 🚪 CIERRES DE RUTA
    Route::prefix('admin')->group(function () {
        Route::resource('cierres', App\Http\Controllers\Admin\CierreRutaController::class)->only(['index', 'show', 'update']);
    });
    
    // 🎛️ ADMIN DASHBOARD (legacy - puedes removerlo si usas el nuevo)
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});

// ========================================
// 🌐 RUTAS PÚBLICAS
// ========================================
Route::get('/', [PublicController::class, 'home'])->name('public.home');

// 📱 DESCARGA DE APK
Route::get('/descargar-app', [App\Http\Controllers\AppDownloadController::class, 'apk'])
    ->name('app.download');

require __DIR__.'/auth.php';