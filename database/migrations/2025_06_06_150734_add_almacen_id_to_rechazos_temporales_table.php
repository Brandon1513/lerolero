<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAlmacenIdToRechazosTemporalesTable extends Migration
{
    public function up()
    {
        Schema::table('rechazos_temporales', function (Blueprint $table) {
            $table->foreignId('almacen_id')->nullable()->constrained('almacenes');
        });
    }

    public function down()
    {
        Schema::table('rechazos_temporales', function (Blueprint $table) {
            $table->dropForeign(['almacen_id']);
            $table->dropColumn('almacen_id');
        });
    }
}

