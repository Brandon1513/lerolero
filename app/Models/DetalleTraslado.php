<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleTraslado extends Model
{
    protected $table = 'detalle_traslado';

    protected $fillable = [
        'traslado_id',
        'producto_id',
        'cantidad',
        'lote',
        'fecha_caducidad',
    ];

    public function traslado()
    {
        return $this->belongsTo(Traslado::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
