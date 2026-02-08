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
        'producto_entregado_id', // 👈 Agrega este campo
        'lote_entregado', // 👈 Agrega este campo
        'fecha_caducidad_entregado', // 👈 Agrega este campo
        'cantidad_entregada', // 👈 Agrega este campo

    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function detalles()
{
    return $this->hasMany(\App\Models\RechazoTemporalDetalle::class, 'rechazo_temporal_id');
}


}
