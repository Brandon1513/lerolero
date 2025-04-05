<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'telefono',
        'asignado_a',
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
}
