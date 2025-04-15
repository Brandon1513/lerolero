<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    public function index()
    {
        $unidades = UnidadMedida::all();
        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'equivalente' => 'required|numeric|min:1',
        ]);

        UnidadMedida::create([
            'nombre' => $request->nombre,
            'equivalente' => $request->equivalente,
            'activo' => true,
        ]);

        return redirect()->route('unidades.index')->with('success', 'Unidad de medida creada correctamente.');
    }

    public function edit(UnidadMedida $unidad)
    {
        return view('unidades.edit', compact('unidad'));
    }

    public function update(Request $request, UnidadMedida $unidad)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'equivalente' => 'required|numeric|min:1',
        ]);

        $unidad->update([
            'nombre' => $request->nombre,
            'equivalente' => $request->equivalente,
        ]);

        return redirect()->route('unidades.index')->with('success', 'Unidad de medida actualizada correctamente.');
    }

    public function destroy(UnidadMedida $unidad)
    {
        $unidad->delete();
        return redirect()->route('unidades.index')->with('success', 'Unidad de medida eliminada.');
    }

    public function toggle(UnidadMedida $unidad)
    {
        $unidad->activo = !$unidad->activo;
        $unidad->save();

        $msg = $unidad->activo ? 'Unidad activada.' : 'Unidad inactivada.';
        return redirect()->route('unidades.index')->with('success', $msg);
    }
}
