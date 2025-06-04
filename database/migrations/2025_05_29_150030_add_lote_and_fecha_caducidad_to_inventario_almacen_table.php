<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventario_almacen', function (Blueprint $table) {
            $table->string('lote')->nullable()->after('cantidad');
            $table->date('fecha_caducidad')->nullable()->after('lote');
        });
    }

    public function down(): void
    {
        Schema::table('inventario_almacen', function (Blueprint $table) {
            $table->dropColumn(['lote', 'fecha_caducidad']);
        });
    }
};

