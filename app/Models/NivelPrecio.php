<?php

// app/Models/NivelPrecio.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelPrecio extends Model
{
    protected $table = 'niveles_precio';
    protected $fillable = ['nombre', 'activo'];

    protected $casts = [
    'activo' => 'boolean',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_nivel_precio', 'nivel_precio_id', 'producto_id')
                    ->withPivot('precio')
                    ->withTimestamps();
    }
}

