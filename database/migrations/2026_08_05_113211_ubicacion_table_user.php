<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Radio en metros para validación de ubicación (default 200m)
            $table->integer('radio_ubicacion')->default(200)->after('ventas_bloqueadas_cierre_id');
            // Si false, no se valida ubicación al iniciar venta
            $table->boolean('validar_ubicacion')->default(true)->after('radio_ubicacion');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['radio_ubicacion', 'validar_ubicacion']);
        });
    }
};
