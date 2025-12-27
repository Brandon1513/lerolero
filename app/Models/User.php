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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function boot()
    {
        parent::boot();

        // Este scope filtra automáticamente solo usuarios activos
        static::addGlobalScope('activo', function (Builder $builder) {
            $builder->where('activo', true);
        });
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function almacen()
    {
        return $this->hasOne(Almacen::class, 'user_id');
    }
    public function clientes()
    {
        return $this->hasMany(Cliente::class, 'asignado_a'); // o el campo que uses
    }
        /**
     * Visitas realizadas por el vendedor
     */
    public function visitas()
    {
        return $this->hasMany(VisitaCliente::class);
    }

    /**
     * Visitas de hoy
     */
    public function visitasHoy()
    {
        return $this->visitas()->whereDate('fecha_visita', today());
    }

    /**
     * Estadísticas de visitas del vendedor
     */
    public function estadisticasVisitas($fechaInicio = null, $fechaFin = null)
    {
        $query = $this->visitas();
        
        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('fecha_visita', [$fechaInicio, $fechaFin]);
        }
        
        $total = $query->count();
        $conVenta = $query->where('realizo_venta', true)->count();
        
        return [
            'total_visitas' => $total,
            'con_venta' => $conVenta,
            'sin_venta' => $total - $conVenta,
            'tasa_conversion' => $total > 0 ? round(($conVenta / $total) * 100, 2) : 0,
        ];
    }
}
