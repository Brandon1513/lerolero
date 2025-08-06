<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Models\NivelPrecio;


class Producto extends Model
{
    protected $fillable = [
        'nombre',
        'marca',
        'categoria_id',
        'unidad_medida_id',
        'precio',
        'imagen',
    ];
    public function nivelesPrecio()
    {
    return $this->belongsToMany(NivelPrecio::class, 'producto_nivel_precio')
                ->withPivot('precio')
                ->withTimestamps();
    }
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class);
    }
    public function preciosPorNivel()
    {
        return $this->hasMany(ProductoNivelPrecio::class);
    }
    public function getImagenUrlAttribute()
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }
    public function promociones()
    {
        return $this->belongsToMany(Promocion::class, 'promocion_producto')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }


}
