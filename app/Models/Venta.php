<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $table = 'ventas';
    
    // ✅ CORRECCIÓN: Separar correctamente los campos
    protected $fillable = [
        'cliente_id',
        'vendedor_id',
        'fecha',
        'total',
        'observaciones',
        'es_credito',
        'total_pagado',
        'saldo_pendiente',
        'estado',
        'fecha_vencimiento',
        'nota_pago',
        'client_tx_id',
        'preventa_id', // 👈 Agrega este campo para relacionar con preventas
    ];
    
    protected $casts = [
        'es_credito'        => 'boolean',
        'total'             => 'decimal:2',
        'total_pagado'      => 'decimal:2',
        'saldo_pendiente'   => 'decimal:2',
        'fecha'             => 'datetime',
        'fecha_vencimiento' => 'date',
    ];

    // ============================================
    // 🔗 RELACIONES
    // ============================================
    
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

    public function promociones()
    {
        return $this->hasMany(\App\Models\VentaPromocion::class);
    }

    public function pagos(): HasMany 
    {
        return $this->hasMany(PagoVenta::class);
    }

    /**
     * Visita asociada a esta venta
     */
    public function visita()
    {
        return $this->hasOne(VisitaCliente::class);
    }
    
    // ============================================
    // 📊 ACCESSORS (Atributos calculados)
    // ============================================
    
    /**
     * Cálculo seguro del total pagado (por si no confías en la columna)
     */
    public function getTotalPagadoComputedAttribute(): float 
    {
        return (float) $this->pagos()->sum('monto');
    }

    /**
     * Cálculo seguro del saldo pendiente
     */
    public function getSaldoComputedAttribute(): float 
    {
        return max(0, (float)$this->total - $this->total_pagado);
    }
}