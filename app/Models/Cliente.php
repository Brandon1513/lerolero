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
        'activo'
    ];

    public function asignadoA()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }
    public function nivelPrecio()
    {
        return $this->belongsTo(NivelPrecio::class, 'nivel_precio_id');
    }

}
