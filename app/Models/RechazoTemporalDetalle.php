<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechazoTemporalDetalle extends Model
{
    protected $table = 'rechazos_temporales_detalle';

    protected $fillable = [
        'rechazo_temporal_id',
        'producto_id',
        'cantidad',
        'lote',
        'fecha_caducidad',
        'almacen_id',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'fecha_caducidad' => 'date',
    ];

    /**
     * Relación con el rechazo temporal (padre)
     */
    public function rechazo()
    {
        return $this->belongsTo(RechazoTemporal::class, 'rechazo_temporal_id');
    }

    /**
     * Relación con el producto de sustitución
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    /**
     * Relación con el almacén de donde se sacó el producto de sustitución
     */
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }
}