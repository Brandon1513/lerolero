<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'activo', 'meses_caducidad'];

    protected $casts = [
        'meses_caducidad' => 'integer',
    ];
}