<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ayudas', function (Blueprint $table) {
            $table->id();
            $table->string('modulo');           // ej: 'producciones', 'inventario', 'cierres', 'vendedores'
            $table->string('plataforma')->default('web'); // 'web' | 'movil' | 'ambas'
            $table->string('titulo');
            $table->string('descripcion_corta')->nullable(); // para el tooltip
            $table->longText('contenido')->nullable();       // HTML rico para la guía completa
            $table->json('pasos')->nullable();               // [{orden, titulo, descripcion, icono}]
            $table->boolean('activo')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();

            $table->unique(['modulo', 'plataforma']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ayudas');
    }
};