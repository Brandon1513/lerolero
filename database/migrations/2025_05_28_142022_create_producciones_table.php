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
        Schema::create('producciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained();
            $table->integer('cantidad');
            $table->date('fecha');
            $table->string('lote')->nullable(); // opcional
            $table->text('notas')->nullable();
            $table->foreignId('usuario_id')->constrained('users'); // quien hizo la producción
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producciones');
    }
};
