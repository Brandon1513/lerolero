<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rechazos_temporales', function (Blueprint $table) {
            $table->string('lote')->nullable()->after('producto_id');
            $table->date('fecha_caducidad')->nullable()->after('lote');
        });
    }

    public function down(): void
    {
        Schema::table('rechazos_temporales', function (Blueprint $table) {
            $table->dropColumn('lote');
            $table->dropColumn('fecha_caducidad');
        });
    }
};

