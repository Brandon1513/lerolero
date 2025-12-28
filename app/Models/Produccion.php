<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produccion extends Model
{
    use HasFactory;

    protected $table = 'producciones';

    protected $fillable = [
        'producto_id',
        'cantidad',
        'fecha',
        'fecha_caducidad', // ✅
        'lote',
        'notas',
        'usuario_id'
    ];

    public function producto() {
        return $this->belongsTo(Producto::class);
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}


