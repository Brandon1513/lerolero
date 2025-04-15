<?php

// app/Models/NivelPrecio.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelPrecio extends Model
{
    protected $table = 'niveles_precio';
    protected $fillable = ['nombre'];

    public function productos()
    {
        return $this->hasMany(ProductoNivelPrecio::class);
    }
}

