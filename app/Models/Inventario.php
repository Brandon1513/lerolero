<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario_almacen';

    protected $fillable = [
        'almacen_id',
        'producto_id',
        'cantidad',
        'lote',
        'fecha_caducidad',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}

