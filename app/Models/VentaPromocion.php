<?php

// app/Models/VentaPromocion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaPromocion extends Model
{
    protected $table = 'venta_promociones';

    protected $fillable = [
        'venta_id',
        'promocion_id',
        'cantidad',
        'precio_promocion',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class);
    }
}
