<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCerradoPorToCierresRutaTable extends Migration
{
    public function up(): void
    {
        Schema::table('cierres_ruta', function (Blueprint $table) {
            $table->unsignedBigInteger('cerrado_por')->nullable()->after('estatus');

            $table->foreign('cerrado_por')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // Si borran el admin, no queremos perder el cierre
        });
    }

    public function down(): void
    {
        Schema::table('cierres_ruta', function (Blueprint $table) {
            $table->dropForeign(['cerrado_por']);
            $table->dropColumn('cerrado_por');
        });
    }
}
