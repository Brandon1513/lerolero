<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\NivelPrecio;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'asignado_a',
        'nivel_precio_id',
        'calle',
        'colonia',
        'codigo_postal',
        'municipio',
        'estado',
        'activo',
        'latitud',
        'longitud',
        'dias_visita',
    ];
    protected $casts = [
        'dias_visita' => 'array',
    ];

    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }
    public function nivelPrecio()
    {
        return $this->belongsTo(NivelPrecio::class, 'nivel_precio_id');
    }
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
        /**
     * Visitas recibidas
     */
    public function visitas()
    {
        return $this->hasMany(VisitaCliente::class);
    }

    /**
     * Última visita realizada
     */
    public function ultimaVisita()
    {
        return $this->hasOne(VisitaCliente::class)->latest('fecha_visita');
    }

    /**
     * Visitas de hoy
     */
    public function visitasHoy()
    {
        return $this->visitas()->whereDate('fecha_visita', today());
    }

    /**
     * ¿Fue visitado hoy?
     */
    public function fueVisitadoHoy()
    {
        return $this->visitasHoy()->exists();
    }

    /**
     * Total de visitas recibidas
     */
    public function getTotalVisitasAttribute()
    {
        return $this->visitas()->count();
    }

    /**
     * Total de visitas con venta
     */
    public function getTotalVisitasConVentaAttribute()
    {
        return $this->visitas()->where('realizo_venta', true)->count();
    }

    /**
     * Tasa de conversión del cliente
     */
    public function getTasaConversionAttribute()
    {
        $total = $this->total_visitas;
        return $total > 0 ? round(($this->total_visitas_con_venta / $total) * 100, 2) : 0;
    }

}
