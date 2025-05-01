<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCambioFieldsToDetalleVentasTable extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->boolean('es_cambio')->default(false)->after('cantidad');
            $table->string('motivo_cambio')->nullable()->after('es_cambio');
        });
    }

    public function down(): void
    {
        Schema::table('detalle_ventas', function (Blueprint $table) {
            $table->dropColumn(['es_cambio', 'motivo_cambio']);
        });
    }
}
