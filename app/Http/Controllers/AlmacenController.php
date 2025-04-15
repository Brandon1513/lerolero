<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Almacen;

class AlmacenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Almacen::with('usuario');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        $almacenes = $query->paginate(10)->withQueryString();

        return view('almacenes.index', compact('almacenes'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vendedores = User::role('vendedor')->get(); // solo para tipo 'vendedor'
        return view('almacenes.create', compact('vendedores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'ubicacion' => 'required|string|max:255',
            'tipo' => 'required|in:general,vendedor',
            'user_id' => $request->tipo === 'vendedor' ? 'required|exists:users,id' : 'nullable',
            'activo' => 'nullable|boolean',
        ]);

        Almacen::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'ubicacion' => $request->ubicacion,
            'tipo' => $request->tipo,
            'user_id' => $request->tipo === 'vendedor' ? $request->user_id : null,
            'activo' => $request->has('activo'),
        ]);

        return redirect()->route('almacenes.index')->with('success', 'Almacén creado correctamente.');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function toggleActivo(Almacen $almacen)
    {
        $almacen->activo = !$almacen->activo;
        $almacen->save();

        $mensaje = $almacen->activo ? 'Almacén activado correctamente.' : 'Almacén inactivado correctamente.';

        return redirect()->route('almacenes.index')->with('success', $mensaje);
    }
}
