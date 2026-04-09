<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cierres_ruta', function (Blueprint $table) {
            $table->timestamp('fecha_desde')->nullable()->after('fecha')
                  ->comment('Inicio del período que cubre este cierre (puede ser del día anterior)');
        });
    }

    public function down(): void
    {
        Schema::table('cierres_ruta', function (Blueprint $table) {
            $table->dropColumn('fecha_desde');
        });
    }
};