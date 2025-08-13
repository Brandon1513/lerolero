<?php

// database/migrations/XXXX_XX_XX_XXXXXX_create_venta_promociones_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('venta_promociones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->foreignId('promocion_id')->constrained('promociones')->restrictOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('precio_promocion', 10, 2); // precio de la promo al momento de vender
            $table->timestamps();

            $table->index(['venta_id', 'promocion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_promociones');
    }
};
