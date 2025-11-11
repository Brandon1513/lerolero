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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('clientes', ClienteController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('inventarios', InventarioController::class);
    Route::patch('/clientes/{cliente}/toggle', [ClienteController::class, 'toggleActivo'])->name('clientes.toggle');
});


//rutas para vendedores
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('vendedores', VendedorController::class)->parameters([
        'vendedores' => 'vendedor' // importante para las rutas tipo model binding
    ]);
    Route::patch('vendedores/{vendedor}/toggle', [VendedorController::class, 'toggleEstado'])->name('vendedores.toggle');

});

//aun no se usa
Route::middleware(['auth', 'role:administrador'])->get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');


//Categorias
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('categorias', \App\Http\Controllers\CategoriaController::class);
    Route::patch('/categorias/{categoria}/toggle', [CategoriaController::class, 'toggle'])->name('categorias.toggle');

});

//Unidades de medida
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('unidades', UnidadMedidaController::class)->parameters([
        'unidades' => 'unidad'
    ]);

    Route::patch('unidades/{unidad}/toggle', [UnidadMedidaController::class, 'toggle'])->name('unidades.toggle');
});


//productos

Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('productos', ProductoController::class)->parameters([
        'productos' => 'producto'
    ]);

    Route::patch('productos/{producto}/toggle', [ProductoController::class, 'toggle'])->name('productos.toggle');
});

Route::middleware(['auth', 'role:administrador'])->group(function () {
    // Niveles de precio
    Route::resource('niveles-precio', NivelPrecioController::class);

    // Almacenes
    Route::resource('almacenes', AlmacenController::class);
    Route::patch('/almacenes/{almacen}/toggle', [AlmacenController::class, 'toggleActivo'])->name('almacenes.toggle');

    // Traslados
    Route::resource('traslados', App\Http\Controllers\TrasladoController::class);
    Route::get('/traslados/{traslado}', [App\Http\Controllers\TrasladoController::class, 'show'])->name('traslados.show');
    Route::get('/traslados/lotes/{almacen}', [\App\Http\Controllers\TrasladoController::class, 'lotesPorAlmacen']);


    // Inventario
    Route::get('/inventario', [App\Http\Controllers\InventarioController::class, 'index'])->name('inventario.index');
    Route::get('/inventario/almacen/{id}', [App\Http\Controllers\InventarioController::class, 'porAlmacen'])->name('inventario.por_almacen');
});



//ventas
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store']);
    Route::get('/ventas/{venta}', [App\Http\Controllers\VentaController::class, 'show'])->name('ventas.show');
    Route::middleware(['auth', 'role:administrador'])->get('/panel-ventas', [VentaController::class, 'panel'])->name('ventas.panel');
});

Route::prefix('admin')->middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('cierres', \App\Http\Controllers\Admin\CierreRutaController::class)->only(['index', 'show', 'update']);
});

//Promociones
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('promociones', PromocionController::class)->parameters([
    'promociones' => 'promocion'
]);

    Route::patch('/promociones/{promocion}/toggle', [PromocionController::class, 'toggle'])->name('promociones.toggle');

});

//public
Route::get('/', [PublicController::class, 'home'])->name('public.home');

// Producciones
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('producciones', \App\Http\Controllers\ProduccionController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('/producciones/{produccion}', [\App\Http\Controllers\ProduccionController::class, 'show'])->name('producciones.show');
});


//APK

// APK (pública)
Route::get('/descargar-app', [\App\Http\Controllers\AppDownloadController::class, 'apk'])
    ->name('app.download');



require __DIR__.'/auth.php';
