<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rechazos_temporales', function (Blueprint $table) {
            $table->timestamp('procesado_en')->nullable()->after('almacen_id');
        });
    }

    public function down(): void
    {
        Schema::table('rechazos_temporales', function (Blueprint $table) {
            $table->dropColumn('procesado_en');
        });
    }
};