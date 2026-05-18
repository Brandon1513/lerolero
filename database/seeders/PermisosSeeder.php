<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermisosSeeder extends Seeder
{
    // ✅ Definición central de módulos y acciones
    // Agregar aquí cualquier módulo nuevo — se reflejará automáticamente en la UI
    public static function modulos(): array
    {
        return [
            'dashboard'        => ['ver'],
            'clientes'         => ['ver', 'crear', 'editar', 'eliminar'],
            'vendedores'       => ['ver', 'crear', 'editar', 'eliminar'],
            'productos'        => ['ver', 'crear', 'editar', 'eliminar'],
            'categorias'       => ['ver', 'crear', 'editar', 'eliminar'],
            'producciones'     => ['ver', 'crear', 'eliminar'],
            'inventario'       => ['ver'],
            'almacenes'        => ['ver', 'crear', 'editar', 'eliminar'],
            'traslados'        => ['ver', 'crear', 'eliminar'],
            'ventas'           => ['ver', 'crear'],
            'promociones'      => ['ver', 'crear', 'editar', 'eliminar'],
            'cierres'          => ['ver', 'cuadrar', 'liberar'],
            'niveles_precio'   => ['ver', 'crear', 'editar', 'eliminar'],
            'unidades'         => ['ver', 'crear', 'editar', 'eliminar'],
            'ayuda'            => ['ver', 'gestionar'],
        ];
    }

    // Permisos por defecto de cada rol
    public static function permisosDeRol(): array
    {
        return [
            'administrador' => '*', // acceso total
            'vendedor'      => [],  // sin acceso web
            'empleado_interno' => [
                'producciones.ver',
                'producciones.crear',
                'inventario.ver',
                'traslados.ver',
                'traslados.crear',
            ],
        ];
    }

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear todos los permisos
        foreach (self::modulos() as $modulo => $acciones) {
            foreach ($acciones as $accion) {
                Permission::firstOrCreate([
                    'name'       => "{$modulo}.{$accion}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // Asignar permisos a roles
        $todosLosPermisos = Permission::all();

        foreach (self::permisosDeRol() as $rolNombre => $permisos) {
            $rol = Role::firstOrCreate(['name' => $rolNombre, 'guard_name' => 'web']);

            if ($permisos === '*') {
                $rol->syncPermissions($todosLosPermisos);
            } else {
                $rol->syncPermissions($permisos);
            }
        }

        $this->command->info('✅ Permisos creados y asignados correctamente.');
    }
}