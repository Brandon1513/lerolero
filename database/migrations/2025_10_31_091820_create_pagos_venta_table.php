<?php
// database/migrations/2025_10_31_000002_create_pagos_venta_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pagos_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->enum('metodo', ['efectivo','transferencia','tarjeta']);
            $table->decimal('monto', 12, 2);
            $table->string('referencia')->nullable();        // folio trans., últimos 4 de tarjeta, etc.
            $table->foreignId('cobrador_id')->nullable()->constrained('users'); // quién registró el pago
            $table->timestamps();

            $table->index(['venta_id', 'metodo']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('pagos_venta');
    }
};
