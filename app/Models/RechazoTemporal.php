<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechazoTemporal extends Model
{
    use HasFactory;

    protected $table = 'rechazos_temporales';

    protected $fillable = [
        'producto_id',
        'vendedor_id',
        'cantidad',
        'motivo',
        'fecha',
        'venta_id', // 👈 Agrega este campo si es necesario para relacionar con una venta
        'lote', // 👈 Agrega este campo
        'fecha_caducidad', // 👈 Agrega este campo
        'almacen_id', // 👈 Agrega este campo para relacionar con el almacén de donde se entregó el producto

    ];

    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'producto_id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    public function detalles()
{
    return $this->hasMany(\App\Models\RechazoTemporalDetalle::class, 'rechazo_temporal_id');
}


}
