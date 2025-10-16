<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('producto_nivel_precio', function (Blueprint $table) {
            $table->unique(['producto_id', 'nivel_precio_id'], 'producto_nivel_unique');
        });
    }
    public function down(): void {
        Schema::table('producto_nivel_precio', function (Blueprint $table) {
            $table->dropUnique('producto_nivel_unique');
        });
    }
};
