<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->unsignedTinyInteger('meses_caducidad')->nullable()->after('nombre')
                  ->comment('Meses de caducidad por defecto para productos de esta categoría');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('meses_caducidad');
        });
    }
};