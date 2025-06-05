<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoteAndFechaCaducidadToDetalleVentasTable extends Migration
{
    public function up()
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->string('lote')->nullable()->after('producto_id');
            $table->date('fecha_caducidad')->nullable()->after('lote');
        });
    }

    public function down()
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn('lote');
            $table->dropColumn('fecha_caducidad');
        });
    }
}
