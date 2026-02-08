<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
{
    Schema::table('rechazos_temporales', function (Blueprint $table) {

        // ✅ NO dropForeign porque NO existe FK en tu BD
        $table->dropColumn([
            'producto_entregado_id',
            'lote_entregado',
            'fecha_caducidad_entregado',
            'cantidad_entregada',
        ]);
    });
}


 public function down(): void
{
    Schema::table('rechazos_temporales', function (Blueprint $table) {
        $table->unsignedBigInteger('producto_entregado_id')->nullable();
        $table->string('lote_entregado', 255)->nullable();
        $table->date('fecha_caducidad_entregado')->nullable();
        $table->decimal('cantidad_entregada', 10, 2)->nullable();
    });
}


};
