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
        'venta_id',
        'lote',
        'fecha_caducidad',
        'almacen_id',
        'procesado_en',
    ];

    protected $casts = [
        'cantidad'     => 'decimal:2',
        'fecha'        => 'date',
        'fecha_caducidad' => 'date',
        'procesado_en' => 'datetime',
    ];

    // ✅ Scope para obtener solo rechazos pendientes (no procesados)
    public function scopePendientes($query)
    {
        return $query->whereNull('procesado_en');
    }

    // ✅ Scope para obtener solo rechazos ya procesados
    public function scopeProcesados($query)
    {
        return $query->whereNotNull('procesado_en');
    }

    /**
     * Relación con el producto devuelto/rechazado
     */
    public function producto()
    {
        return $this->belongsTo(\App\Models\Producto::class, 'producto_id');
    }

    /**
     * Relación con el vendedor que realizó el cambio
     */
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    /**
     * Relación con la venta donde ocurrió el cambio
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    /**
     * Relación con el almacén del vendedor
     */
    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_id');
    }

    /**
     * ✅ Relación con los detalles (productos de sustitución)
     * Esta es la clave para obtener los productos que se dieron a cambio
     */
    public function detalles()
    {
        return $this->hasMany(\App\Models\RechazoTemporalDetalle::class, 'rechazo_temporal_id');
    }
}