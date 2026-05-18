<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class VendedorController extends Controller
{
    public function index(Request $request)
    {
        $vendedores = User::query()
            ->withoutGlobalScopes()
            ->when($request->filled('rol'), fn($q) => $q->role($request->rol))
            ->when($request->filled('nombre'), fn($q) =>
                $q->where('name', 'like', "%{$request->nombre}%")
            )
            ->when($request->estado === 'activo', fn($q) => $q->where('activo', true))
            ->when($request->estado === 'inactivo', fn($q) => $q->where('activo', false))
            ->orderBy('name')
            ->get()
            ->map(function ($u) {
                $tieneVentas = \DB::getSchemaBuilder()->hasTable('ventas')
                    ? \DB::table('ventas')->where('vendedor_id', $u->id)->exists()
                    : false;

                $tieneAlmacenVendedor = \DB::getSchemaBuilder()->hasTable('almacenes')
                    ? \DB::table('almacenes')->where('user_id', $u->id)->exists()
                    : false;

                $tieneTraslados = \DB::getSchemaBuilder()->hasTable('traslados')
                    ? \DB::table('traslados')->where('almacen_destino_id', function($q) use ($u) {
                        $q->select('id')->from('almacenes')->where('user_id', $u->id)->limit(1);
                    })->exists()
                    : false;

                $u->puede_eliminar = !($tieneVentas || $tieneAlmacenVendedor || $tieneTraslados);
                $u->tiene_movimientos = !$u->puede_eliminar;
                return $u;
            });

        return view('vendedores.index', compact('vendedores'));
    }

    public function create()
    {
        // ✅ Roles desde BD — siempre actualizados automáticamente
        $roles = Role::orderBy('name')->get();
        return view('vendedores.create', compact('roles'));
    }

    public function store(Request $request)
    {
        // ✅ Validar contra roles reales de la BD
        $rolesValidos = Role::pluck('name')->implode(',');

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles'    => 'required|array|min:1',
            'roles.*'  => 'in:' . $rolesValidos,
        ]);

        $vendedor = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'activo'   => true,
        ]);

        $vendedor->syncRoles($request->roles);

        return redirect()->route('vendedores.index')
            ->with('success', 'Usuario registrado correctamente con rol(es): ' . implode(', ', $request->roles));
    }

    public function edit($vendedor)
    {
        $vendedor = User::withoutGlobalScope('activo')->findOrFail($vendedor);
        $roles = Role::orderBy('name')->get();
        return view('vendedores.edit', compact('vendedor', 'roles'));
    }

    public function update(Request $request, $vendedor)
    {
        $vendedor = User::withoutGlobalScope('activo')->findOrFail($vendedor);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $vendedor->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles'    => 'required|array',
            'roles.*'  => 'exists:roles,name',
        ]);

        $data = $request->only('name', 'email');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $vendedor->update($data);
        $vendedor->syncRoles($request->roles);

        return redirect()->route('vendedores.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $vendedor)
    {
        if (auth()->id() === $vendedor->id) {
            return redirect()->route('vendedores.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        if ($vendedor->hasRole('administrador')) {
            $adminsCount = User::withoutGlobalScope('activo')->role('administrador')->count();
            if ($adminsCount <= 1) {
                return redirect()->route('vendedores.index')
                    ->with('error', 'No se puede eliminar: es el último administrador del sistema.');
            }
            $vendedor->activo = false;
            $vendedor->save();
            return redirect()->route('vendedores.index')
                ->with('error', 'No se puede eliminar un administrador. Se inactivó en su lugar.');
        }

        $tieneVentas = DB::getSchemaBuilder()->hasTable('ventas')
            ? DB::table('ventas')->where('vendedor_id', $vendedor->id)->exists()
            : false;

        $tieneAlmacen = DB::getSchemaBuilder()->hasTable('almacenes')
            ? DB::table('almacenes')->where('user_id', $vendedor->id)->exists()
            : false;

        if ($tieneVentas || $tieneAlmacen) {
            $vendedor->activo = false;
            $vendedor->save();
            return redirect()->route('vendedores.index')
                ->with('error', 'No se puede eliminar: el usuario ya tiene movimientos. Se inactivó en su lugar.');
        }

        $vendedor->delete();
        return redirect()->route('vendedores.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    public function toggleEstado($vendedor)
    {
        $vendedor = User::withoutGlobalScope('activo')->findOrFail($vendedor);

        if (auth()->id() === $vendedor->id) {
            return redirect()->route('vendedores.index')
                ->with('error', 'No puedes cambiar el estado de tu propio usuario.');
        }

        if ($vendedor->hasRole('administrador')) {
            $adminsActivos = User::withoutGlobalScope('activo')
                ->where('activo', true)
                ->role('administrador')
                ->count();

            if ($vendedor->activo && $adminsActivos <= 1) {
                return redirect()->route('vendedores.index')
                    ->with('error', 'No puedes inactivar al último administrador activo.');
            }
        }

        $vendedor->activo = !(bool) $vendedor->activo;
        $vendedor->save();

        $msg = $vendedor->activo ? 'Usuario activado correctamente.' : 'Usuario inactivado correctamente.';
        return redirect()->route('vendedores.index')->with('success', $msg);
    }
}