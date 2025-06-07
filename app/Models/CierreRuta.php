<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CierreRuta extends Model
{
    use HasFactory;
    protected $table = 'cierres_ruta'; // 👈 Esto soluciona el problema

    protected $fillable = [
        'total_efectivo',
        'observaciones',
        'estatus',
        'cerrado_por',
        'inventario_inicial',
        'inventario_final',
        'cambios',
        'traslado_id',
        'total_ventas',
        'vendedor_id',
        'fecha'
    ];

    protected $casts = [
        'inventario_inicial' => 'array',
        'inventario_final' => 'array',
        'cambios' => 'array',
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
    public function cerradoPor()
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }
}
