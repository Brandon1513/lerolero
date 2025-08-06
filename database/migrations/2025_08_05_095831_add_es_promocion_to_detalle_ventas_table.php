<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->boolean('es_promocion')->default(false)->after('venta_id');
        });
    }

    public function down()
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn('es_promocion');
        });
    }

};
