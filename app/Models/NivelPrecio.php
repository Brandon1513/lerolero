<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelPrecio extends Model
{
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_nivel_precio')
                    ->withPivot('precio')
                    ->withTimestamps();
    }
}
