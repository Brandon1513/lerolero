<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\VendedorController;

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

Route::middleware(['role:administrador'])->group(function () {
    Route::resource('clientes', ClienteController::class);
    Route::resource('productos', ProductoController::class);
    Route::resource('inventarios', InventarioController::class);
});

Route::patch('/clientes/{cliente}/toggle', [ClienteController::class, 'toggleActivo'])->name('clientes.toggle');

//rutas para vendedores
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('vendedores', VendedorController::class)->parameters([
        'vendedores' => 'vendedor' // importante para las rutas tipo model binding
    ]);
    Route::patch('vendedores/{vendedor}/toggle', [VendedorController::class, 'toggleEstado'])->name('vendedores.toggle');

});


Route::middleware(['auth', 'role:administrador'])->get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');


//Categorias
Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::resource('categorias', \App\Http\Controllers\CategoriaController::class);
});


require __DIR__.'/auth.php';
