<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    protected $table = 'almacenes'; // 👈 Esto soluciona el problema
    protected $fillable = [
        'nombre',
        'descripcion',
        'ubicacion',
        'tipo', // puede ser 'general' o 'vendedor'
        'user_id', // solo si es tipo vendedor
        'activo',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
    public function inventario()
{
    return $this->hasMany(\App\Models\Inventario::class);
}
}
