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
            $table->unsignedBigInteger('nivel_precio_id')->nullable()->after('asignado_a');
    
            $table->foreign('nivel_precio_id')
                ->references('id')->on('niveles_precio')
                ->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropForeign(['nivel_precio_id']);
            $table->dropColumn('nivel_precio_id');
        });
    }
    
};
