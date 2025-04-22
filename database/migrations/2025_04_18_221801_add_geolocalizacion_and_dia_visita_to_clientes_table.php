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
        Schema::table('clientes', function (Blueprint $table) {
            $table->decimal('latitud', 10, 7)->nullable()->after('telefono');
            $table->decimal('longitud', 10, 7)->nullable()->after('latitud');
            $table->string('dia_visita')->nullable()->after('longitud'); // Ejemplo: Lunes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['latitud', 'longitud', 'dia_visita']);
        });
    }
};
