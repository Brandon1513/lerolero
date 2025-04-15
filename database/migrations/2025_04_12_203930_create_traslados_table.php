<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('traslados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_origen_id')->constrained('almacenes')->onDelete('cascade');
            $table->foreignId('almacen_destino_id')->constrained('almacenes')->onDelete('cascade');
            $table->date('fecha');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_traslado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_id')->constrained('traslados')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_traslado');
        Schema::dropIfExists('traslados');
    }
};
