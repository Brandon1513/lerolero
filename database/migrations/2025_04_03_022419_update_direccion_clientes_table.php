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
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('calle')->nullable();
            $table->string('colonia')->nullable();
            $table->string('codigo_postal')->nullable();
            // Opcionales:
            $table->string('municipio')->nullable();
            $table->string('estado')->nullable();
            
            // Si quieres eliminar el campo viejo:
            $table->dropColumn('direccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
