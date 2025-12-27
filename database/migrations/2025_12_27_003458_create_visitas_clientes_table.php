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
        Schema::create('visitas_clientes', function (Blueprint $table) {
            $table->id();
            
            // Relación con usuario (vendedor)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Vendedor que realizó la visita');
            
            // Relación con cliente
            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->onDelete('cascade')
                ->comment('Cliente visitado');
            
            // Información de la visita
            $table->date('fecha_visita')
                ->comment('Fecha de la visita');
            
            $table->time('hora_visita')
                ->nullable()
                ->comment('Hora de la visita');
            
            // ¿Se realizó venta?
            $table->boolean('realizo_venta')
                ->default(false)
                ->comment('Si se concretó una venta en esta visita');
            
            // Si hubo venta, referencia opcional
            $table->foreignId('venta_id')
                ->nullable()
                ->constrained('ventas')
                ->onDelete('set null')
                ->comment('ID de la venta si se realizó una');
            
            // Motivo si NO hubo venta
            $table->enum('motivo_no_venta', [
                'sin_dinero',
                'sin_stock_deseado',
                'precios_altos',
                'cliente_ausente',
                'cliente_no_necesita',
                'otro'
            ])->nullable()->comment('Razón por la que no se realizó venta');
            
            $table->text('observaciones')
                ->nullable()
                ->comment('Notas adicionales de la visita');
            
            // Ubicación donde se registró la visita
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            
            // Estado de la visita
            $table->enum('estado', ['pendiente', 'visitado', 'cancelado'])
                ->default('pendiente');
            
            $table->timestamps();
            
            // Índices para optimizar consultas
            $table->index(['user_id', 'fecha_visita']);
            $table->index(['cliente_id', 'fecha_visita']);
            $table->index('realizo_venta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas_clientes');
    }
};