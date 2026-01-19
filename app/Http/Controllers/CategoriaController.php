<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    // Mostrar todas las categorías
    public function index()
    {
        $categorias = Categoria::orderBy('nombre')
            ->get()
            ->map(function ($c) {
                // ✅ tiene productos asociados => no se puede eliminar
                $tieneProductos = Producto::where('categoria_id', $c->id)->exists();

                $c->puede_eliminar = ! $tieneProductos;
                $c->tiene_movimientos = $tieneProductos;

                return $c;
            });

        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
        ]);

        Categoria::create([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
        ]);

        $categoria->update([
            'nombre' => $request->nombre,
        ]);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    // Eliminar una categoría (solo si no tiene productos)
    public function destroy(Categoria $categoria)
    {
        $tieneProductos = Producto::where('categoria_id', $categoria->id)->exists();

        if ($tieneProductos) {
            // Mejor práctica: inactivar en lugar de borrar
            $categoria->activo = false;
            $categoria->save();

            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar: la categoría ya tiene productos asociados. Se inactivó en su lugar.');
        }

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }

    // activar/desactivar una categoría
    public function toggle(Categoria $categoria)
    {
        $categoria->activo = ! $categoria->activo;
        $categoria->save();

        $mensaje = $categoria->activo ? 'Categoría activada correctamente.' : 'Categoría inactivada correctamente.';
        return redirect()->route('categorias.index')->with('success', $mensaje);
    }
}
