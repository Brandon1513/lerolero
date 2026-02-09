<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->foreignId('preventa_id')
                ->nullable()
                ->after('id')
                ->constrained('preventas')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->index('preventa_id');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropForeign(['preventa_id']);
            $table->dropColumn('preventa_id');
        });
    }
};
