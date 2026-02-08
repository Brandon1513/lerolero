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

    public function rechazo()
    {
        return $this->belongsTo(RechazoTemporal::class, 'rechazo_temporal_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
