<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    use HasFactory;

    protected $table = 'promociones';

    protected $fillable = [
        'nombre',
        'precio',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
        'activo'       => 'boolean',
    ];

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'promocion_producto', 'promocion_id', 'producto_id')
                    ->withPivot('cantidad')
                    ->withTimestamps();
    }

    public function ventas()
    {
        return $this->hasMany(\App\Models\VentaPromocion::class);
    }

    /* =========================
     * Helpers de Vigencia
     * ========================= */

    // proxima | vigente | expirada
    public function getVigenciaEstadoAttribute(): string
    {
        $hoy = now()->startOfDay();

        if ($this->fecha_inicio && $hoy->lt($this->fecha_inicio)) {
            return 'proxima';
        }
        if ($this->fecha_fin && $hoy->gt($this->fecha_fin)) {
            return 'expirada';
        }
        return 'vigente';
    }

    public function getVigenciaLabelAttribute(): string
    {
        return match ($this->vigencia_estado) {
            'proxima'  => 'Próxima',
            'expirada' => 'Expirada',
            default    => 'Vigente',
        };
    }

    // (Opcional) solo vigentes hoy
    public function scopeVigentes($query)
    {
        $hoy = now()->toDateString();

        return $query->where(function ($q) use ($hoy) {
            $q->whereNull('fecha_inicio')
              ->orWhereDate('fecha_inicio', '<=', $hoy);
        })->where(function ($q) use ($hoy) {
            $q->whereNull('fecha_fin')
              ->orWhereDate('fecha_fin', '>=', $hoy);
        });
    }
}
