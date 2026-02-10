<?php
// php artisan make:migration add_ventas_bloqueadas_to_users_table --table=users

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('users', function (Blueprint $table) {
      $table->boolean('ventas_bloqueadas')->default(false)->after('password');
      $table->timestamp('ventas_bloqueadas_desde')->nullable()->after('ventas_bloqueadas');
      $table->string('ventas_bloqueadas_motivo')->nullable()->after('ventas_bloqueadas_desde');
      $table->unsignedBigInteger('ventas_bloqueadas_cierre_id')->nullable()->after('ventas_bloqueadas_motivo');

      $table->index('ventas_bloqueadas');
    });
  }

  public function down(): void {
    Schema::table('users', function (Blueprint $table) {
      $table->dropIndex(['ventas_bloqueadas']);
      $table->dropColumn([
        'ventas_bloqueadas',
        'ventas_bloqueadas_desde',
        'ventas_bloqueadas_motivo',
        'ventas_bloqueadas_cierre_id',
      ]);
    });
  }
};
