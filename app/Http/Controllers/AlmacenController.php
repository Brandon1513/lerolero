<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Almacen;

class AlmacenController extends Controller
{
    public function index(Request $request)
    {
        $statsTotal      = Almacen::count();
        $statsGenerales  = Almacen::where('tipo', 'general')->count();
        $statsVendedores = Almacen::where('tipo', 'vendedor')->count();
        $statsInactivos  = Almacen::where('activo', false)->count();

        $query = Almacen::with('usuario');

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%'.$request->buscar.'%');
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        $almacenes = $query->orderBy('nombre')->paginate(15)->withQueryString();

        return view('almacenes.index', compact(
            'almacenes', 'statsTotal', 'statsGenerales', 'statsVendedores', 'statsInactivos'
        ));
    }

    public function create()
    {
        $usuarios = User::orderBy('name')->get();
        return view('almacenes.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'ubicacion'   => 'nullable|string|max:255',
            'tipo'        => 'required|in:general,vendedor,rechazo',
            'user_id'     => 'nullable|exists:users,id',
            'activo'      => 'nullable|boolean',
        ]);

        Almacen::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'ubicacion'   => $request->ubicacion,
            'tipo'        => $request->tipo,
            'user_id'     => $request->tipo === 'vendedor' ? $request->user_id : null,
            'activo'      => $request->has('activo'),
        ]);

        return redirect()->route('almacenes.index')->with('success', 'Almacén creado correctamente.');
    }

    public function show(Almacen $almacen)
    {
        //
    }

    public function edit(Almacen $almacen)
    {
        $usuarios = User::orderBy('name')->get();
        return view('almacenes.edit', compact('almacen', 'usuarios'));
    }

    // ✅ ESTE ERA EL PROBLEMA: string $id → Almacen $almacen + implementado
    public function update(Request $request, Almacen $almacen)
    {
        $request->validate([
            'nombre'    => 'required|string|max:255',
            'ubicacion' => 'nullable|string|max:255',
            'tipo'      => 'required|in:general,vendedor,rechazo',
            'user_id'   => 'nullable|exists:users,id',
        ]);

        $almacen->update([
            'nombre'    => $request->nombre,
            'ubicacion' => $request->ubicacion,
            'tipo'      => $request->tipo,
            'user_id'   => $request->tipo === 'vendedor' ? $request->user_id : null,
        ]);

        return redirect()->route('almacenes.index')->with('success', 'Almacén actualizado correctamente.');
    }

    public function destroy(Almacen $almacen)
    {
        $almacen->delete();
        return redirect()->route('almacenes.index')->with('success', 'Almacén eliminado.');
    }

    public function toggleActivo(Almacen $almacen)
    {
        $almacen->activo = !(bool) $almacen->activo;
        $almacen->save();
        return redirect()->route('almacenes.index')->with('success', 'Estado actualizado.');
    }
}