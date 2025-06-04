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
        Schema::create('lotes_inventario', function (Blueprint $table) {
        $table->id();
        $table->foreignId('producto_id')->constrained();
        $table->foreignId('almacen_id')->constrained('almacenes'); // ahora sí referencia la tabla correcta
        $table->string('lote')->nullable();
        $table->date('fecha_caducidad')->nullable();
        $table->integer('cantidad');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lotes_inventario');
    }
};
