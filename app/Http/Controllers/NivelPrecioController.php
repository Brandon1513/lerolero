<?php

namespace App\Http\Controllers;

use App\Models\NivelPrecio;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NivelPrecioController extends Controller
{
    public function index()
    {
        $niveles = NivelPrecio::orderBy('nombre')
            ->get()
            ->map(function ($n) {

                $tieneClientes = Cliente::where('nivel_precio_id', $n->id)->exists();

                $tieneProductos = DB::getSchemaBuilder()->hasTable('producto_nivel_precio')
                    ? DB::table('producto_nivel_precio')->where('nivel_precio_id', $n->id)->exists()
                    : false;

                $n->puede_eliminar = !($tieneClientes || $tieneProductos);
                $n->tiene_uso = ! $n->puede_eliminar;

                return $n;
            });

        return view('niveles_precio.index', compact('niveles'));
    }

    public function create()
    {
        return view('niveles_precio.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:niveles_precio,nombre',
        ]);

        NivelPrecio::create([
            'nombre' => $request->nombre,
            'activo' => true,
        ]);

        return redirect()->route('niveles_precio.index')->with('success', 'Nivel creado correctamente.');
    }

    public function edit(NivelPrecio $niveles_precio)
    {
        return view('niveles_precio.edit', ['nivel' => $niveles_precio]);
    }

    public function update(Request $request, NivelPrecio $niveles_precio)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:niveles_precio,nombre,' . $niveles_precio->id,
        ]);

        $niveles_precio->update([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('niveles_precio.index')->with('success', 'Nivel actualizado correctamente.');
    }

    public function destroy(NivelPrecio $niveles_precio)
    {
        $tieneClientes = Cliente::where('nivel_precio_id', $niveles_precio->id)->exists();

        $tieneProductos = DB::getSchemaBuilder()->hasTable('producto_nivel_precio')
            ? DB::table('producto_nivel_precio')->where('nivel_precio_id', $niveles_precio->id)->exists()
            : false;

        if ($tieneClientes || $tieneProductos) {
            $niveles_precio->activo = false;
            $niveles_precio->save();

            return redirect()->route('niveles-precio.index')
                ->with('error', 'No se puede eliminar: el nivel ya está en uso (clientes/productos). Se inactivó en su lugar.');
        }

        $niveles_precio->delete();

        return redirect()->route('niveles_precio.index')->with('success', 'Nivel eliminado correctamente.');
    }

    public function toggle(NivelPrecio $niveles_precio)
    {
        $niveles_precio->activo = ! (bool) $niveles_precio->activo;
        $niveles_precio->save();

        return back()->with('success', 'Nivel ' . ($niveles_precio->activo ? 'activado' : 'inactivado') . ' correctamente.');
    }
}
