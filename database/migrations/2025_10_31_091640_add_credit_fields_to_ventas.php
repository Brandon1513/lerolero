<?php

// database/migrations/2025_10_31_000001_add_credit_fields_to_ventas.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('ventas', function (Blueprint $table) {
            $table->boolean('es_credito')->default(false)->after('total');
            $table->decimal('total_pagado', 12, 2)->default(0)->after('es_credito');
            $table->decimal('saldo_pendiente', 12, 2)->default(0)->after('total_pagado');
            $table->enum('estado', ['pendiente','parcial','pagada','cancelada'])->default('pendiente')->after('saldo_pendiente');
            $table->date('fecha_vencimiento')->nullable()->after('estado');
            $table->string('nota_pago')->nullable()->after('fecha_vencimiento'); // opcional
        });
    }

    public function down(): void {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['es_credito','total_pagado','saldo_pendiente','estado','fecha_vencimiento','nota_pago']);
        });
    }
};
