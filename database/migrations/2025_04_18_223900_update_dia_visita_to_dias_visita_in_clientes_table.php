<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Elimina el campo anterior si ya existe
            if (Schema::hasColumn('clientes', 'dia_visita')) {
                $table->dropColumn('dia_visita');
            }

            // Agrega el nuevo campo json
            $table->json('dias_visita')->nullable()->after('longitud');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn('dias_visita');
            $table->string('dia_visita')->nullable()->after('longitud');
        });
    }
};

