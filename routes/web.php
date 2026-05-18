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
use App\Http\Controllers\Admin\CierreRutaController;
use App\Http\Controllers\Admin\AyudaController;
use App\Http\Controllers\Admin\PermisoUsuarioController;
use App\Http\Controllers\DashboardController;

// ========================================
//  DASHBOARD
// ========================================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:administrador'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('/mi-inicio', function () {
        $user = auth()->user();
        if ($user->hasRole('empleado_interno') && !$user->hasRole('administrador')) {
            return redirect()->route('producciones.index');
        }
        return redirect()->route('dashboard');
    })->name('inicio.redirect');
});

// ========================================
// PERFIL DE USUARIO
// ========================================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ========================================
//  AYUDA — rutas públicas (sin auth)
// ========================================
Route::get('/centro-ayuda', [AyudaController::class, 'publico'])->name('ayuda.publico');
Route::get('/centro-ayuda/{modulo}/{plataforma?}', [AyudaController::class, 'modulo'])->name('ayuda.modulo');

// ========================================
//  RUTAS EXCLUSIVAS DE ADMINISTRADOR
//  (gestión del sistema, no operativas)
// ========================================
Route::middleware(['auth', 'role:administrador'])->group(function () {

    // VENDEDORES
    Route::resource('vendedores', VendedorController::class)->parameters(['vendedores' => 'vendedor']);
    Route::patch('vendedores/{vendedor}/toggle', [VendedorController::class, 'toggleEstado'])->name('vendedores.toggle');

    // PERMISOS POR USUARIO
    Route::get('/vendedores/{usuario}/permisos', [PermisoUsuarioController::class, 'edit'])->name('vendedores.permisos.edit');
    Route::put('/vendedores/{usuario}/permisos', [PermisoUsuarioController::class, 'update'])->name('vendedores.permisos.update');

    // CIERRES — cuadrar y liberar solo admin
    Route::patch('/admin/cierres/{cierre}', [CierreRutaController::class, 'update'])->name('cierres.update');
    Route::post('/cierres/{cierre}/liberar-ventas', [CierreRutaController::class, 'liberarVentas'])->name('cierres.liberar');

    // AYUDA — CRUD
    Route::resource('ayuda', AyudaController::class)->except(['show']);

    // ADMIN DASHBOARD
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');

    // INVENTARIOS CRUD completo
    Route::resource('inventarios', InventarioController::class);
});

// ========================================
//  RUTAS COMPARTIDAS — admin + empleado_interno
//  El middleware 'permiso:modulo.accion' verifica que el usuario
//  tenga ese permiso (por rol o por asignación directa del admin).
//  El administrador siempre pasa (ver CheckModuloPermiso.php).
//  Así el admin puede dar acceso granular sin tocar código.
// ========================================
Route::middleware(['auth', 'role:administrador|empleado_interno'])->group(function () {

    // ── CLIENTES ──────────────────────────────────────────────────
    Route::get('/clientes', [ClienteController::class, 'index'])
        ->middleware('permiso:clientes.ver')->name('clientes.index');
    Route::get('/clientes/create', [ClienteController::class, 'create'])
        ->middleware('permiso:clientes.crear')->name('clientes.create');
    Route::post('/clientes', [ClienteController::class, 'store'])
        ->middleware('permiso:clientes.crear')->name('clientes.store');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])
        ->middleware('permiso:clientes.ver')->name('clientes.show');
    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])
        ->middleware('permiso:clientes.editar')->name('clientes.edit');
    Route::patch('/clientes/{cliente}', [ClienteController::class, 'update'])
        ->middleware('permiso:clientes.editar')->name('clientes.update');
    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])
        ->middleware('permiso:clientes.eliminar')->name('clientes.destroy');
    Route::patch('/clientes/{cliente}/toggle', [ClienteController::class, 'toggleActivo'])
        ->middleware('permiso:clientes.editar')->name('clientes.toggle');

    // ── PRODUCTOS ─────────────────────────────────────────────────
    Route::get('/productos', [ProductoController::class, 'index'])
        ->middleware('permiso:productos.ver')->name('productos.index');
    Route::get('/productos/create', [ProductoController::class, 'create'])
        ->middleware('permiso:productos.crear')->name('productos.create');
    Route::post('/productos', [ProductoController::class, 'store'])
        ->middleware('permiso:productos.crear')->name('productos.store');
    Route::get('/productos/{producto}', [ProductoController::class, 'show'])
        ->middleware('permiso:productos.ver')->name('productos.show');
    Route::get('/productos/{producto}/edit', [ProductoController::class, 'edit'])
        ->middleware('permiso:productos.editar')->name('productos.edit');
    Route::patch('/productos/{producto}', [ProductoController::class, 'update'])
        ->middleware('permiso:productos.editar')->name('productos.update');
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])
        ->middleware('permiso:productos.eliminar')->name('productos.destroy');
    Route::patch('productos/{producto}/toggle', [ProductoController::class, 'toggle'])
        ->middleware('permiso:productos.editar')->name('productos.toggle');

    // ── CATEGORÍAS ────────────────────────────────────────────────
    Route::get('/categorias', [CategoriaController::class, 'index'])
        ->middleware('permiso:categorias.ver')->name('categorias.index');
    Route::get('/categorias/create', [CategoriaController::class, 'create'])
        ->middleware('permiso:categorias.crear')->name('categorias.create');
    Route::post('/categorias', [CategoriaController::class, 'store'])
        ->middleware('permiso:categorias.crear')->name('categorias.store');
    Route::get('/categorias/{categoria}', [CategoriaController::class, 'show'])
        ->middleware('permiso:categorias.ver')->name('categorias.show');
    Route::get('/categorias/{categoria}/edit', [CategoriaController::class, 'edit'])
        ->middleware('permiso:categorias.editar')->name('categorias.edit');
    Route::patch('/categorias/{categoria}', [CategoriaController::class, 'update'])
        ->middleware('permiso:categorias.editar')->name('categorias.update');
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])
        ->middleware('permiso:categorias.eliminar')->name('categorias.destroy');
    Route::patch('/categorias/{categoria}/toggle', [CategoriaController::class, 'toggle'])
        ->middleware('permiso:categorias.editar')->name('categorias.toggle');

    // ── UNIDADES DE MEDIDA ────────────────────────────────────────
    Route::get('/unidades', [UnidadMedidaController::class, 'index'])
        ->middleware('permiso:unidades.ver')->name('unidades.index');
    Route::get('/unidades/create', [UnidadMedidaController::class, 'create'])
        ->middleware('permiso:unidades.crear')->name('unidades.create');
    Route::post('/unidades', [UnidadMedidaController::class, 'store'])
        ->middleware('permiso:unidades.crear')->name('unidades.store');
    Route::get('/unidades/{unidad}', [UnidadMedidaController::class, 'show'])
        ->middleware('permiso:unidades.ver')->name('unidades.show');
    Route::get('/unidades/{unidad}/edit', [UnidadMedidaController::class, 'edit'])
        ->middleware('permiso:unidades.editar')->name('unidades.edit');
    Route::patch('/unidades/{unidad}', [UnidadMedidaController::class, 'update'])
        ->middleware('permiso:unidades.editar')->name('unidades.update');
    Route::delete('/unidades/{unidad}', [UnidadMedidaController::class, 'destroy'])
        ->middleware('permiso:unidades.eliminar')->name('unidades.destroy');
    Route::patch('unidades/{unidad}/toggle', [UnidadMedidaController::class, 'toggle'])
        ->middleware('permiso:unidades.editar')->name('unidades.toggle');

    // ── NIVELES DE PRECIO ─────────────────────────────────────────
    Route::get('/niveles-precio', [NivelPrecioController::class, 'index'])
        ->middleware('permiso:niveles_precio.ver')->name('niveles-precio.index');
    Route::get('/niveles-precio/create', [NivelPrecioController::class, 'create'])
        ->middleware('permiso:niveles_precio.crear')->name('niveles-precio.create');
    Route::post('/niveles-precio', [NivelPrecioController::class, 'store'])
        ->middleware('permiso:niveles_precio.crear')->name('niveles-precio.store');
    Route::get('/niveles-precio/{niveles_precio}', [NivelPrecioController::class, 'show'])
        ->middleware('permiso:niveles_precio.ver')->name('niveles-precio.show');
    Route::get('/niveles-precio/{niveles_precio}/edit', [NivelPrecioController::class, 'edit'])
        ->middleware('permiso:niveles_precio.editar')->name('niveles-precio.edit');
    Route::patch('/niveles-precio/{niveles_precio}', [NivelPrecioController::class, 'update'])
        ->middleware('permiso:niveles_precio.editar')->name('niveles-precio.update');
    Route::delete('/niveles-precio/{niveles_precio}', [NivelPrecioController::class, 'destroy'])
        ->middleware('permiso:niveles_precio.eliminar')->name('niveles-precio.destroy');
    Route::patch('niveles-precio/{niveles_precio}/toggle', [NivelPrecioController::class, 'toggle'])
        ->middleware('permiso:niveles_precio.editar')->name('niveles-precio.toggle');

    // ── ALMACENES ─────────────────────────────────────────────────
    Route::get('/almacenes', [AlmacenController::class, 'index'])
        ->middleware('permiso:almacenes.ver')->name('almacenes.index');
    Route::get('/almacenes/create', [AlmacenController::class, 'create'])
        ->middleware('permiso:almacenes.crear')->name('almacenes.create');
    Route::post('/almacenes', [AlmacenController::class, 'store'])
        ->middleware('permiso:almacenes.crear')->name('almacenes.store');
    Route::get('/almacenes/{almacen}', [AlmacenController::class, 'show'])
        ->middleware('permiso:almacenes.ver')->name('almacenes.show');
    Route::get('/almacenes/{almacen}/edit', [AlmacenController::class, 'edit'])
        ->middleware('permiso:almacenes.editar')->name('almacenes.edit');
    Route::patch('/almacenes/{almacen}', [AlmacenController::class, 'update'])
        ->middleware('permiso:almacenes.editar')->name('almacenes.update');
    Route::delete('/almacenes/{almacen}', [AlmacenController::class, 'destroy'])
        ->middleware('permiso:almacenes.eliminar')->name('almacenes.destroy');
    Route::patch('/almacenes/{almacen}/toggle', [AlmacenController::class, 'toggleActivo'])
        ->middleware('permiso:almacenes.editar')->name('almacenes.toggle');

    // ── VENTAS ────────────────────────────────────────────────────
    Route::get('/ventas', [VentaController::class, 'index'])
        ->middleware('permiso:ventas.ver')->name('ventas.index');
    Route::get('/ventas/create', [VentaController::class, 'create'])
        ->middleware('permiso:ventas.crear')->name('ventas.create');
    Route::post('/ventas', [VentaController::class, 'store'])
        ->middleware('permiso:ventas.crear')->name('ventas.store');
    Route::get('/ventas/{venta}', [VentaController::class, 'show'])
        ->middleware('permiso:ventas.ver')->name('ventas.show');
    Route::get('/panel-ventas', [VentaController::class, 'panel'])
        ->middleware('permiso:ventas.ver')->name('ventas.panel');

    // ── PROMOCIONES ───────────────────────────────────────────────
    Route::get('/promociones', [PromocionController::class, 'index'])
        ->middleware('permiso:promociones.ver')->name('promociones.index');
    Route::get('/promociones/create', [PromocionController::class, 'create'])
        ->middleware('permiso:promociones.crear')->name('promociones.create');
    Route::post('/promociones', [PromocionController::class, 'store'])
        ->middleware('permiso:promociones.crear')->name('promociones.store');
    Route::get('/promociones/{promocion}', [PromocionController::class, 'show'])
        ->middleware('permiso:promociones.ver')->name('promociones.show');
    Route::get('/promociones/{promocion}/edit', [PromocionController::class, 'edit'])
        ->middleware('permiso:promociones.editar')->name('promociones.edit');
    Route::patch('/promociones/{promocion}', [PromocionController::class, 'update'])
        ->middleware('permiso:promociones.editar')->name('promociones.update');
    Route::delete('/promociones/{promocion}', [PromocionController::class, 'destroy'])
        ->middleware('permiso:promociones.eliminar')->name('promociones.destroy');
    Route::patch('/promociones/{promocion}/toggle', [PromocionController::class, 'toggle'])
        ->middleware('permiso:promociones.editar')->name('promociones.toggle');

    // ── PRODUCCIONES ──────────────────────────────────────────────
    Route::get('/producciones', [App\Http\Controllers\ProduccionController::class, 'index'])
        ->middleware('permiso:producciones.ver')->name('producciones.index');
    Route::get('/producciones/create', [App\Http\Controllers\ProduccionController::class, 'create'])
        ->middleware('permiso:producciones.crear')->name('producciones.create');
    Route::post('/producciones', [App\Http\Controllers\ProduccionController::class, 'store'])
        ->middleware('permiso:producciones.crear')->name('producciones.store');
    Route::get('/producciones/{produccion}', [App\Http\Controllers\ProduccionController::class, 'show'])
        ->middleware('permiso:producciones.ver')->name('producciones.show');
    Route::delete('/producciones/{produccion}', [App\Http\Controllers\ProduccionController::class, 'destroy'])
        ->middleware('permiso:producciones.eliminar')->name('producciones.destroy');

    // ── INVENTARIO ────────────────────────────────────────────────
    Route::get('/inventario', [App\Http\Controllers\InventarioController::class, 'index'])
        ->middleware('permiso:inventario.ver')->name('inventario.index');
    Route::get('/inventario/almacen/{id}', [App\Http\Controllers\InventarioController::class, 'porAlmacen'])
        ->middleware('permiso:inventario.ver')->name('inventario.por_almacen');

    // ── TRASLADOS ─────────────────────────────────────────────────
    Route::get('/traslados', [App\Http\Controllers\TrasladoController::class, 'index'])
        ->middleware('permiso:traslados.ver')->name('traslados.index');
    Route::get('/traslados/create', [App\Http\Controllers\TrasladoController::class, 'create'])
        ->middleware('permiso:traslados.crear')->name('traslados.create');
    Route::post('/traslados', [App\Http\Controllers\TrasladoController::class, 'store'])
        ->middleware('permiso:traslados.crear')->name('traslados.store');
    Route::get('/traslados/{traslado}', [App\Http\Controllers\TrasladoController::class, 'show'])
        ->middleware('permiso:traslados.ver')->name('traslados.show');
    Route::post('/traslados/{traslado}/firma', [App\Http\Controllers\TrasladoController::class, 'guardarFirma'])
        ->middleware('permiso:traslados.crear')->name('traslados.firma');
    Route::delete('/traslados/{traslado}', [App\Http\Controllers\TrasladoController::class, 'destroy'])
        ->middleware('permiso:traslados.eliminar')->name('traslados.destroy');
    Route::get('/traslados/lotes/{almacen}', [App\Http\Controllers\TrasladoController::class, 'lotesPorAlmacen']);

    // ── CIERRES ──────────────────────────────────────────────────
    Route::get('/cierres', [App\Http\Controllers\Admin\CierreRutaController::class, 'index'])
        ->middleware('permiso:cierres.ver')->name('cierres.index');
    Route::get('/cierres/{cierre}', [App\Http\Controllers\Admin\CierreRutaController::class, 'show'])
        ->middleware('permiso:cierres.ver')->name('cierres.show');
});

// ========================================
//  RUTAS PÚBLICAS
// ========================================
Route::get('/descargar-app', [App\Http\Controllers\AppDownloadController::class, 'apk'])
    ->name('app.download');

// ========================================
//  RUTA PRINCIPAL (home público)
// ========================================
Route::get('/', [PublicController::class, 'home'])->name('public.home');

require __DIR__.'/auth.php';