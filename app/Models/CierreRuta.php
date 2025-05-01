<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CierreRuta extends Model
{
    use HasFactory;
    protected $table = 'cierres_ruta'; // 👈 Esto soluciona el problema

    protected $fillable = [
        'vendedor_id',
        'fecha',
        'total_ventas',
        'total_efectivo',
        'inventario_inicial',
        'inventario_final',
        'cambios',
        'observaciones',
        'estatus',
        'cerrado_por', // <- agregar aquí
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
