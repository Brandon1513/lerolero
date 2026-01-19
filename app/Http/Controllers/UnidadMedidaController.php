<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use App\Models\Producto;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    public function index()
    {
        $unidades = UnidadMedida::orderBy('nombre')
            ->get()
            ->map(function ($u) {
                //  Si está usada por productos, NO es eliminable
                $tieneProductos = Producto::where('unidad_medida_id', $u->id)->exists();

                $u->puede_eliminar = ! $tieneProductos;
                $u->tiene_movimientos = $tieneProductos;

                return $u;
            });

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
        $tieneProductos = Producto::where('unidad_medida_id', $unidad->id)->exists();

        if ($tieneProductos) {
            // Mejor práctica: inactivar en vez de borrar
            $unidad->activo = false;
            $unidad->save();

            return redirect()->route('unidades.index')
                ->with('error', 'No se puede eliminar: la unidad ya está asignada a uno o más productos. Se inactivó en su lugar.');
        }

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
