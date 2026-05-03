<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ayuda extends Model
{
    protected $fillable = [
        'modulo',
        'plataforma',
        'titulo',
        'descripcion_corta',
        'contenido',
        'pasos',
        'imagenes',
        'activo',
        'orden',
    ];

    protected $casts = [
        'pasos'    => 'array',
        'imagenes' => 'array',
        'activo'   => 'boolean',
    ];

    public static function modulos(): array
    {
        return [
            'producciones' => ['icono' => '🏭', 'label' => 'Producciones',  'ruta' => 'producciones.index'],
            'inventario'   => ['icono' => '📦', 'label' => 'Inventario',    'ruta' => 'inventario.index'],
            'traslados'    => ['icono' => '🚚', 'label' => 'Traslados',     'ruta' => 'traslados.index'],
            'ventas'       => ['icono' => '🧾', 'label' => 'Ventas',        'ruta' => 'ventas.index'],
            'cierres'      => ['icono' => '📋', 'label' => 'Cierres',       'ruta' => 'cierres.index'],
            'clientes'     => ['icono' => '👥', 'label' => 'Clientes',      'ruta' => 'clientes.index'],
            'productos'    => ['icono' => '🍬', 'label' => 'Productos',     'ruta' => 'productos.index'],
            'vendedores'   => ['icono' => '🧑‍💼', 'label' => 'Vendedores', 'ruta' => 'users.index'],
            'app_movil'    => ['icono' => '📱', 'label' => 'App Móvil',     'ruta' => null],
        ];
    }

    public static function paraPlatforma(string $plataforma): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('activo', true)
            ->whereIn('plataforma', [$plataforma, 'ambas'])
            ->orderBy('orden')
            ->get();
    }
}