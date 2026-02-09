<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Almacen;
use App\Models\Venta;


class Preventa extends Model
{
    protected $table = 'preventas';

    protected $fillable = [
        'folio',
        'cliente_id',
        'vendedor_id',
        'almacen_id',
        'total',
        'total_pagado',
        'saldo_pendiente',
        'es_credito',
        'fecha_vencimiento',
        'status',
        'printed_at',
        'converted_at',
        'client_tx_id',
        'payload',
        'venta_id',
    ];

    protected $casts = [
        'payload' => 'array',
        'es_credito' => 'boolean',
        'total' => 'float',
        'total_pagado' => 'float',
        'saldo_pendiente' => 'float',
        'fecha_vencimiento' => 'date',
        'printed_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
