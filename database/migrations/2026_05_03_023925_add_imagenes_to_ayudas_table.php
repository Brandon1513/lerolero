<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ayudas', function (Blueprint $table) {
            // Imágenes generales de la guía (galería al final)
            // [{url, caption, tipo: 'upload'|'url'}]
            $table->json('imagenes')->nullable()->after('pasos');
        });

        // También agregar imagen por paso — se guarda dentro del JSON de pasos
        // No necesita columna extra, se agrega al array de pasos existente
    }

    public function down(): void
    {
        Schema::table('ayudas', function (Blueprint $table) {
            $table->dropColumn('imagenes');
        });
    }
};