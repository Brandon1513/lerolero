<?php

// app/Models/PagoVenta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoVenta extends Model
{
    protected $table = 'pagos_venta';
    protected $fillable = ['venta_id','metodo','monto','referencia','cobrador_id'];

    public function venta(): BelongsTo {
        return $this->belongsTo(Venta::class);
    }

    public function cobrador(): BelongsTo {
        return $this->belongsTo(User::class, 'cobrador_id');
    }
}
