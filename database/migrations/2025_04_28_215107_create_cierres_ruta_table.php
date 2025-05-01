<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cierres_ruta', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendedor_id');
            $table->date('fecha');
            $table->decimal('total_ventas', 10, 2);
            $table->decimal('total_efectivo', 10, 2)->nullable();
            $table->json('inventario_inicial')->nullable(); // Guardar inventario que se llevó
            $table->json('inventario_final')->nullable();   // Guardar inventario que quedó
            $table->json('cambios')->nullable();             // Guardar los cambios
            $table->text('observaciones')->nullable();
            $table->enum('estatus', ['pendiente', 'cuadrado'])->default('pendiente');
            $table->timestamps();
        
            $table->foreign('vendedor_id')->references('id')->on('users')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cierres_ruta');
    }
};
