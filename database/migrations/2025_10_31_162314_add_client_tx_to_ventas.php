<?php

// database/migrations/xxxx_add_client_tx_id_to_ventas.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('client_tx_id', 64)->nullable()->unique();
        });
    }
    public function down(): void {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropUnique(['client_tx_id']);
            $table->dropColumn('client_tx_id');
        });
    }
};
