<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public function nivelesPrecio()
    {
    return $this->belongsToMany(NivelPrecio::class, 'producto_nivel_precio')
                ->withPivot('precio')
                ->withTimestamps();
    }
}
