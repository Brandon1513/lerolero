<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('preventas', function (Blueprint $table) {
            $table->id();

            $table->string('folio', 50)->unique()->nullable();

            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnUpdate();
            $table->foreignId('vendedor_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('almacen_id')->constrained('almacenes')->cascadeOnUpdate();

            // snapshot financiero (para comparar)
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('total_pagado', 12, 2)->default(0);
            $table->decimal('saldo_pendiente', 12, 2)->default(0);
            $table->boolean('es_credito')->default(false);
            $table->date('fecha_vencimiento')->nullable();

            // estado preventa
            // borrador | impresa | convertida | cancelada
            $table->string('status', 20)->default('impresa');
            $table->timestamp('printed_at')->nullable();
            $table->timestamp('converted_at')->nullable();

            // para idempotencia / rastreo
            $table->string('client_tx_id', 64)->nullable()->index();

            // payload completo (carrito, promos, pagos, rechazos_ids, observaciones, etc.)
            $table->json('payload')->nullable();

            // relación a venta final (cuando se convierte)
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->nullOnDelete()->cascadeOnUpdate();

            $table->timestamps();

            $table->index(['vendedor_id', 'cliente_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preventas');
    }
};
