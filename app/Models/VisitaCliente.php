<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitaCliente extends Model
{
    use HasFactory;

    protected $table = 'visitas_clientes';

    protected $fillable = [
        'user_id',
        'cliente_id',
        'fecha_visita',
        'hora_visita',
        'realizo_venta',
        'venta_id',
        'motivo_no_venta',
        'observaciones',
        'latitud',
        'longitud',
        'estado',
    ];

    protected $casts = [
        'fecha_visita' => 'date',
        'realizo_venta' => 'boolean',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    // ============================================
    // 🔗 RELACIONES
    // ============================================
    
    /**
     * Vendedor que realizó la visita
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cliente visitado
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    /**
     * Venta asociada (si se realizó)
     */
    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    // ============================================
    // 🔍 SCOPES (Consultas útiles)
    // ============================================
    
    /**
     * Visitas de hoy
     */
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_visita', today());
    }

    /**
     * Visitas con venta realizada
     */
    public function scopeConVenta($query)
    {
        return $query->where('realizo_venta', true);
    }

    /**
     * Visitas sin venta
     */
    public function scopeSinVenta($query)
    {
        return $query->where('realizo_venta', false);
    }

    /**
     * Visitas de un vendedor específico
     */
    public function scopeDelVendedor($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Visitas en un rango de fechas
     */
    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_visita', [$fechaInicio, $fechaFin]);
    }

    /**
     * Visitas por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Visitas con un motivo específico de no venta
     */
    public function scopePorMotivo($query, $motivo)
    {
        return $query->where('motivo_no_venta', $motivo);
    }

    // ============================================
    // 📊 ACCESSORS (Atributos calculados)
    // ============================================
    
    /**
     * Determina si la visita fue exitosa (venta realizada y vinculada)
     */
    public function getEsVisitaExitosaAttribute(): bool
    {
        return $this->realizo_venta && $this->venta_id !== null;
    }

    /**
     * Devuelve el nombre legible del motivo de no venta
     */
    public function getMotivoNoVentaLegibleAttribute(): ?string
    {
        if (!$this->motivo_no_venta) {
            return null;
        }

        $motivos = [
            'sin_dinero' => 'Sin dinero',
            'sin_stock_deseado' => 'Sin stock deseado',
            'precios_altos' => 'Precios altos',
            'cliente_ausente' => 'Cliente ausente',
            'cliente_no_necesita' => 'No necesita producto',
            'otro' => 'Otro motivo',
        ];

        return $motivos[$this->motivo_no_venta] ?? 'Desconocido';
    }

    /**
     * Hora de visita formateada
     */
    public function getHoraVisitaFormateadaAttribute(): ?string
    {
        if (!$this->hora_visita) {
            return null;
        }

        return \Carbon\Carbon::parse($this->hora_visita)->format('h:i A');
    }

    // ============================================
    // 📈 MÉTODOS ESTÁTICOS DE ESTADÍSTICAS
    // ============================================
    
    /**
     * Obtener tasa de conversión de un vendedor en un período
     */
    public static function tasaConversion($userId, $fechaInicio = null, $fechaFin = null)
    {
        $query = self::delVendedor($userId);

        if ($fechaInicio && $fechaFin) {
            $query->entreFechas($fechaInicio, $fechaFin);
        } elseif ($fechaInicio) {
            $query->whereDate('fecha_visita', '>=', $fechaInicio);
        } elseif ($fechaFin) {
            $query->whereDate('fecha_visita', '<=', $fechaFin);
        }

        $total = $query->count();
        $conVenta = $query->clone()->conVenta()->count();

        return $total > 0 ? round(($conVenta / $total) * 100, 2) : 0;
    }

    /**
     * Obtener motivos más frecuentes de no venta
     */
    public static function motivosMasFrecuentes($userId, $limit = 5)
    {
        return self::delVendedor($userId)
            ->sinVenta()
            ->whereNotNull('motivo_no_venta')
            ->selectRaw('motivo_no_venta, COUNT(*) as total')
            ->groupBy('motivo_no_venta')
            ->orderByDesc('total')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                $motivos = [
                    'sin_dinero' => '💰 Sin dinero',
                    'sin_stock_deseado' => '📦 Sin stock deseado',
                    'precios_altos' => '💸 Precios altos',
                    'cliente_ausente' => '🚪 Cliente ausente',
                    'cliente_no_necesita' => '✋ No necesita',
                    'otro' => '📝 Otro',
                ];
                
                return [
                    'motivo' => $item->motivo_no_venta,
                    'motivo_legible' => $motivos[$item->motivo_no_venta] ?? 'Desconocido',
                    'total' => $item->total,
                ];
            });
    }

    /**
     * Clientes más difíciles (muchas visitas sin venta)
     */
    public static function clientesDificiles($userId, $limit = 10)
    {
        return self::delVendedor($userId)
            ->with('cliente:id,nombre')
            ->sinVenta()
            ->selectRaw('cliente_id, COUNT(*) as total_visitas_sin_venta')
            ->groupBy('cliente_id')
            ->orderByDesc('total_visitas_sin_venta')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'cliente_id' => $item->cliente_id,
                    'cliente_nombre' => $item->cliente->nombre ?? 'N/D',
                    'total_visitas_sin_venta' => $item->total_visitas_sin_venta,
                ];
            });
    }
}