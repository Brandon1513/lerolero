<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrasladoIdToCierresRutaTable extends Migration
{
    public function up(): void
    {
        Schema::table('cierres_ruta', function (Blueprint $table) {
            $table->unsignedBigInteger('traslado_id')->nullable()->after('cerrado_por');

            $table->foreign('traslado_id')->references('id')->on('traslados')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cierres_ruta', function (Blueprint $table) {
            $table->dropForeign(['traslado_id']);
            $table->dropColumn('traslado_id');
        });
    }
}
