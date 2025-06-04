<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produccion extends Model
{
    use HasFactory;
    protected $table = 'producciones'; // <- agrega esta línea

    protected $fillable = ['producto_id', 'cantidad', 'fecha', 'lote', 'notas', 'usuario_id'];

    public function producto() {
        return $this->belongsTo(Producto::class);
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}

