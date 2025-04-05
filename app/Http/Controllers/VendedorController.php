<?php


namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class VendedorController extends Controller
{
    public function index(Request $request)
{
    $vendedores = User::query()
        ->when($request->rol, fn($q) => $q->role($request->rol))
        ->when($request->nombre, fn($q) =>
            $q->where('name', 'like', "%{$request->nombre}%"))
        ->when($request->estado === 'activo', fn($q) =>
            $q->where('activo', true))
        ->when($request->estado === 'inactivo', fn($q) =>
            $q->where('activo', false))
        ->get();

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
        'role' => 'required|in:vendedor,administrador',
    ]);

    $vendedor = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'activo' => true,
    ]);

    // Asignar el rol seleccionado desde el formulario
    $vendedor->assignRole($request->role);

    return redirect()->route('vendedores.index')->with('success', 'Usuario registrado correctamente con rol: ' . $request->role);
    }


    public function edit(User $vendedor)
    {
    $roles = \Spatie\Permission\Models\Role::all();
    return view('vendedores.edit', compact('vendedor', 'roles'));
    }

    public function update(Request $request, User $vendedor)
    {
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
        $vendedor->delete();
        return redirect()->route('vendedores.index')->with('success', 'Vendedor eliminado correctamente.');
    }
    public function toggleEstado(User $vendedor)
    {
    $vendedor->activo = !$vendedor->activo;
    $vendedor->save();

    $msg = $vendedor->activo ? 'Vendedor activado correctamente.' : 'Vendedor inactivado correctamente.';
    return redirect()->route('vendedores.index')->with('success', $msg);
    }

}
