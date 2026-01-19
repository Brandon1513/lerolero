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
            ->withoutGlobalScopes() // 👈 clave si tienes scope de activo en User
            ->when($request->filled('rol'), fn($q) => $q->role($request->rol))
            ->when($request->filled('nombre'), fn($q) =>
                $q->where('name', 'like', "%{$request->nombre}%")
            )
            ->when($request->estado === 'activo', fn($q) => $q->where('activo', true))
            ->when($request->estado === 'inactivo', fn($q) => $q->where('activo', false))
            ->orderBy('name')
            ->get()
            ->map(function ($u) {

                // ✅ Bloquear borrado si tiene movimientos/relaciones
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

                // Si tiene algo vinculado, no permitir eliminar
                $u->puede_eliminar = !($tieneVentas || $tieneAlmacenVendedor || $tieneTraslados);
                $u->tiene_movimientos = !$u->puede_eliminar;

                return $u;
            });

        return view('vendedores.index', compact('vendedores'));
    }



    public function create()
    {
        return view('vendedores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array|min:1',
            'roles.*' => 'in:vendedor,administrador',
        ]);

        $vendedor = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'activo' => true,
        ]);

        // Asignar roles múltiples
        $vendedor->syncRoles($request->roles);

        return redirect()->route('vendedores.index')
            ->with('success', 'Usuario registrado correctamente con rol(es): ' . implode(', ', $request->roles));
    }


    public function edit($vendedor)
    {
        $vendedor = User::withoutGlobalScope('activo')->findOrFail($vendedor);
        $roles = Role::all();
        return view('vendedores.edit', compact('vendedor', 'roles'));
    }

    public function update(Request $request, $vendedor)
{
    $vendedor = User::withoutGlobalScope('activo')->findOrFail($vendedor);

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $vendedor->id,
        'password' => 'nullable|string|min:8|confirmed',
        'roles' => 'required|array',
        'roles.*' => 'exists:roles,name',
    ]);

    $data = $request->only('name', 'email');

    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $vendedor->update($data);
    $vendedor->syncRoles($request->roles);

    return redirect()->route('vendedores.index')->with('success', 'Vendedor actualizado correctamente.');
}

    public function destroy(User $vendedor)
{
    // 1) No permitir que el usuario se borre a sí mismo
    if (auth()->id() === $vendedor->id) {
        return redirect()->route('vendedores.index')
            ->with('error', 'No puedes eliminar tu propio usuario.');
    }

    // 2) Si es administrador, NO permitir borrado
    if ($vendedor->hasRole('administrador')) {

        // Extra: nunca permitir eliminar al último admin
        $adminsCount = User::withoutGlobalScope('activo')->role('administrador')->count();
        if ($adminsCount <= 1) {
            return redirect()->route('vendedores.index')
                ->with('error', 'No se puede eliminar: es el último administrador del sistema.');
        }

        // Recomendación: en vez de borrar, inactivar
        $vendedor->activo = false;
        $vendedor->save();

        return redirect()->route('vendedores.index')
            ->with('error', 'No se puede eliminar un administrador. Se inactivó en su lugar.');
    }

    // 3) Bloquear borrado si tiene movimientos
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
            ->with('error', 'No se puede eliminar: el vendedor ya tiene movimientos (ventas/almacén). Se inactivó en su lugar.');
    }

    // 4) Si no hay bloqueos, ahora sí borrar
    $vendedor->delete();

    return redirect()->route('vendedores.index')
        ->with('success', 'Vendedor eliminado correctamente.');
}

    public function toggleEstado($vendedor)
{
    $vendedor = User::withoutGlobalScope('activo')->findOrFail($vendedor);

    // No permitir inactivar tu propio usuario
    if (auth()->id() === $vendedor->id) {
        return redirect()->route('vendedores.index')
            ->with('error', 'No puedes cambiar el estado de tu propio usuario.');
    }

    // Si es admin, no permitir dejar al sistema sin admins activos
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

    $vendedor->activo = ! (bool) $vendedor->activo;
    $vendedor->save();

    $msg = $vendedor->activo ? 'Vendedor activado correctamente.' : 'Vendedor inactivado correctamente.';
    return redirect()->route('vendedores.index')->with('success', $msg);
}



}
