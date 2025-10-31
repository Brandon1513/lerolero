<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Venta extends Model
{
    protected $table = 'ventas';
    protected $fillable = [
        'cliente_id','vendedor_id','fecha','total','observaciones',
        'es_credito','total_pagado','saldo_pendiente','estado','fecha_vencimiento','nota_pago, client_tx_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }
    public function rechazos()
    {
        return $this->hasMany(\App\Models\RechazoTemporal::class, 'venta_id');
    }

    // 👇 tabla venta_promociones: {id, venta_id, promocion_id, cantidad, precio_promocion, ...}
    public function promociones()
    {
        return $this->hasMany(\App\Models\VentaPromocion::class);
    }

     public function pagos(): HasMany {
        return $this->hasMany(PagoVenta::class);
    }

    // Cálculos seguros si en algún momento no quieres depender de columnas persistidas:
    public function getTotalPagadoComputedAttribute(): float {
        return (float) $this->pagos()->sum('monto');
    }

    public function getSaldoComputedAttribute(): float {
        return max(0, (float)$this->total - $this->total_pagado);
    }

}
