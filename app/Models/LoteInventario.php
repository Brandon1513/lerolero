<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteInventario extends Model
{
    protected $table = 'lotes_inventario';

    protected $fillable = [
        'producto_id',
        'almacen_id',
        'lote',
        'fecha_caducidad',
        'cantidad',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
}
