<?php

// database/migrations/xxxx_add_fecha_caducidad_to_producciones_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('producciones', function (Blueprint $table) {
            $table->date('fecha_caducidad')->nullable()->after('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('producciones', function (Blueprint $table) {
            $table->dropColumn('fecha_caducidad');
        });
    }
};
