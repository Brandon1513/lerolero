<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $table = 'inventario_almacen'; // Asegúrate que coincide con tu tabla

    protected $fillable = [
        'almacen_id',
        'producto_id',
        'cantidad',
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
