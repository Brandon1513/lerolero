<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rechazos_temporales', function (Blueprint $table) {
            $table->unsignedBigInteger('producto_entregado_id')->nullable()->after('producto_id');
            $table->decimal('cantidad_entregada', 10, 2)->nullable()->after('cantidad');
            $table->string('lote_entregado')->nullable()->after('lote');
            $table->date('fecha_caducidad_entregado')->nullable()->after('fecha_caducidad');

            $table->index(['producto_entregado_id']);
        });
    }

    public function down(): void
    {
        Schema::table('rechazos_temporales', function (Blueprint $table) {
            $table->dropIndex(['producto_entregado_id']);
            $table->dropColumn([
                'producto_entregado_id',
                'cantidad_entregada',
                'lote_entregado',
                'fecha_caducidad_entregado',
            ]);
        });
    }
};
