<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traslado extends Model
{
    protected $fillable = [
        'almacen_origen_id',
        'almacen_destino_id',
        'fecha',
        'observaciones',
        'user_id',
        'firma_base64',
    ];

    public function origen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_origen_id');
    }

    public function destino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleTraslado::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}