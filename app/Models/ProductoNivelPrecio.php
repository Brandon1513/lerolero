<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoNivelPrecio extends Model
{
    protected $table = 'producto_nivel_precio';

    protected $fillable = [
        'producto_id',
        'nivel_precio_id',
        'precio',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function nivelPrecio()
    {
        return $this->belongsTo(NivelPrecio::class);
    }
    public function preciosPorNivel()
{
    return $this->hasMany(ProductoNivelPrecio::class);
}

}
