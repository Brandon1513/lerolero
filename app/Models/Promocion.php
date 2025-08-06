<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    protected $table = 'promociones';

    protected $fillable = [
        'nombre',
        'precio',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'promocion_producto')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }
}
