<?php

// database/migrations/2026_02_07_000001_create_rechazos_temporales_detalle_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rechazos_temporales_detalle', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rechazo_temporal_id')
                ->constrained('rechazos_temporales')
                ->cascadeOnDelete();

            $table->foreignId('producto_id')->constrained('productos');
            $table->decimal('cantidad', 10, 2)->default(0);

            $table->string('lote', 255)->nullable();
            $table->date('fecha_caducidad')->nullable();

            $table->foreignId('almacen_id')->nullable()->constrained('almacenes');

            $table->timestamps();

            $table->index(['rechazo_temporal_id']);
            $table->index(['almacen_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rechazos_temporales_detalle');
    }
};

