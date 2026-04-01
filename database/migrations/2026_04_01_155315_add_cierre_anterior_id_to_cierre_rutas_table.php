<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cierres_ruta', function (Blueprint $table) {
            $table->unsignedBigInteger('cierre_anterior_id')
                  ->nullable()
                  ->after('traslado_id')
                  ->comment('ID del cierre previo del mismo día/vendedor (cuando hay reapertura)');

            $table->foreign('cierre_anterior_id')
                  ->references('id')
                  ->on('cierres_ruta')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cierres_ruta', function (Blueprint $table) {
            $table->dropForeign(['cierre_anterior_id']);
            $table->dropColumn('cierre_anterior_id');
        });
    }
};