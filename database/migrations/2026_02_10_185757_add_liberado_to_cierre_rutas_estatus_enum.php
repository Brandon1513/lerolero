<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE cierres_ruta
            MODIFY estatus 
            ENUM('pendiente','liberado','cuadrado') 
            NOT NULL 
            DEFAULT 'pendiente'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE cierres_ruta
            MODIFY estatus 
            ENUM('pendiente','cuadrado') 
            NOT NULL 
            DEFAULT 'pendiente'
        ");
    }
};
