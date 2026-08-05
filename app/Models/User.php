<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'activo',
        'ventas_bloqueadas',
        'ventas_bloqueadas_desde',
        'ventas_bloqueadas_motivo',
        'ventas_bloqueadas_cierre_id',
        'radio_ubicacion',   // ✅ Radio en metros para validación de ubicación
        'validar_ubicacion', // ✅ Si false, no se valida ubicación al iniciar venta
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('activo', function (Builder $builder) {
            $builder->where('activo', true);
        });
    }

    protected $casts = [
        'email_verified_at'        => 'datetime',
        'ventas_bloqueadas'        => 'boolean',
        'ventas_bloqueadas_desde'  => 'datetime',
        'validar_ubicacion'        => 'boolean', // ✅
    ];

    public function almacen()
    {
        return $this->hasOne(Almacen::class, 'user_id');
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'asignado_a');
    }

    public function visitas()
    {
        return $this->hasMany(VisitaCliente::class);
    }

    public function visitasHoy()
    {
        return $this->visitas()->whereDate('fecha_visita', today());
    }

    public function estadisticasVisitas($fechaInicio = null, $fechaFin = null)
    {
        $query = $this->visitas();

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha_visita', [$fechaInicio, $fechaFin]);
        }

        $total = $query->count();
        $conVenta = $query->where('realizo_venta', true)->count();

        return [
            'total_visitas'   => $total,
            'con_venta'       => $conVenta,
            'sin_venta'       => $total - $conVenta,
            'tasa_conversion' => $total > 0 ? round(($conVenta / $total) * 100, 2) : 0,
        ];
    }
}