<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('traslados', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('observaciones');
            $table->text('firma_base64')->nullable()->after('user_id'); // firma digital en base64
        });
    }

    public function down(): void
    {
        Schema::table('traslados', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'firma_base64']);
        });
    }
};