<?php
namespace App\Http\Controllers;

use App\Models\NivelPrecio;
use Illuminate\Http\Request;

class NivelPrecioController extends Controller
{
    public function index()
    {
        $niveles = NivelPrecio::all();
        return view('niveles_precio.index', compact('niveles'));
    }

    public function create()
    {
        return view('niveles_precio.create');
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        NivelPrecio::create($request->all());

        return redirect()->route('niveles-precio.index')->with('success', 'Nivel de precio creado correctamente.');
    }

    public function edit(NivelPrecio $niveles_precio)
    {
        return view('niveles_precio.edit', ['nivel' => $niveles_precio]);
    }

    public function update(Request $request, NivelPrecio $niveles_precio)
    {
        $request->validate(['nombre' => 'required|string|max:255']);
        $niveles_precio->update($request->all());

        return redirect()->route('niveles-precio.index')->with('success', 'Nivel actualizado.');
    }

    public function destroy(NivelPrecio $niveles_precio)
    {
        $niveles_precio->delete();

        return redirect()->route('niveles-precio.index')->with('success', 'Nivel eliminado.');
    }
}
