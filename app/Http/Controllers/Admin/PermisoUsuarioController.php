<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Database\Seeders\PermisosSeeder;

class PermisoUsuarioController extends Controller
{
    // Vista de gestión de permisos de un usuario
    public function edit(User $usuario)
    {
        $modulos     = PermisosSeeder::modulos();
        $permisosRol = $usuario->getPermissionsViaRoles()->pluck('name')->toArray();
        $permisosDirectos = $usuario->getDirectPermissions()->pluck('name')->toArray();

        // Todos los permisos efectivos del usuario (rol + directos)
        $permisosEfectivos = $usuario->getAllPermissions()->pluck('name')->toArray();

        return view('vendedores.permisos', compact(
            'usuario',
            'modulos',
            'permisosRol',
            'permisosDirectos',
            'permisosEfectivos'
        ));
    }

    // Guardar cambios de permisos directos
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'permisos'   => 'nullable|array',
            'permisos.*' => 'exists:permissions,name',
        ]);

        $permisosSeleccionados = $request->input('permisos', []);

        // ✅ Solo guardamos permisos DIRECTOS (excepciones al rol)
        // Los permisos del rol se mantienen automáticamente
        $usuario->syncPermissions($permisosSeleccionados);

        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('vendedores.permisos.edit', $usuario)
            ->with('success', "Permisos de {$usuario->name} actualizados correctamente.");
    }
}