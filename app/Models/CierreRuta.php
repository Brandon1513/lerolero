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
        'cierre_anterior_id',
        'total_ventas',
        'vendedor_id',
        'fecha',
        'fecha_desde',
    ];


    protected $casts = [
        'inventario_inicial' => 'array',
        'inventario_final'   => 'array',
        'cambios'            => 'array',
        'fecha_desde'        => 'datetime',
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function cerradoPor()
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    // Cierre previo del mismo día (cuando este es una reapertura)
    public function cierreAnterior()
    {
        return $this->belongsTo(CierreRuta::class, 'cierre_anterior_id');
    }

    // Cierres que tienen este como anterior (reaperturas de este cierre)
    public function reaperturas()
    {
        return $this->hasMany(CierreRuta::class, 'cierre_anterior_id');
    }
}