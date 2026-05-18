<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmpleadoInternoRolSeeder extends Seeder
{
    public function run(): void
    {
        // Crear el rol si no existe
        $rol = Role::firstOrCreate(['name' => 'empleado_interno', 'guard_name' => 'web']);

        // Permisos que puede hacer
        $permisos = [
            'producciones.index',
            'producciones.create',
            'producciones.store',
            'producciones.show',
            'traslados.index',
            'traslados.create',
            'traslados.store',
            'traslados.show',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        $rol->syncPermissions($permisos);

        $this->command->info('Rol empleado_interno creado con permisos de producciones y traslados.');
    }
}