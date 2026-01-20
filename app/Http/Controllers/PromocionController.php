<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocionController extends Controller
{
    public function index()
    {
        $promociones = Promocion::with('productos')
            ->withCount('productos')
            ->latest()
            ->paginate(10);

        return view('promociones.index', compact('promociones'));
    }

    public function create()
    {
        $productos = Producto::where('activo', true)->get();
        return view('promociones.create', compact('productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
            'precio_promocional' => 'required|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'productos' => 'required|array',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $promocion = Promocion::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio_promocional,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'activo' => true,
        ]);

        $datosProductos = [];
        foreach ($request->productos as $id => $data) {
            $datosProductos[$id] = ['cantidad' => $data['cantidad']];
        }

        $promocion->productos()->sync($datosProductos);

        return redirect()->route('promociones.index')->with('success', 'Promoción creada con éxito.');
    }

    public function edit(Promocion $promocion)
    {
        $productos = Producto::where('activo', true)->get();

        // clave = producto_id, valor = cantidad
        $productosSeleccionados = $promocion->productos->pluck('pivot.cantidad', 'id')->toArray();

        return view('promociones.edit', compact('promocion', 'productos', 'productosSeleccionados'));
    }

    public function update(Request $request, Promocion $promocion)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
            'precio' => 'required|numeric|min:0',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'productos' => 'required|array',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        $promocion->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'precio' => $request->precio,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ]);

        $datosProductos = [];
        foreach ($request->productos as $id => $data) {
            $datosProductos[$id] = ['cantidad' => $data['cantidad']];
        }

        $promocion->productos()->sync($datosProductos);

        return redirect()->route('promociones.index')->with('success', 'Promoción actualizada.');
    }

    public function destroy(Promocion $promocion)
{
    $tieneVentas = DB::table('venta_promociones')
        ->where('promocion_id', $promocion->id)
        ->exists();

    if ($tieneVentas) {
        // ✅ Solo inactivar (NO quitar productos)
        $promocion->activo = false;
        $promocion->save();

        return redirect()
            ->route('promociones.index')
            ->with('error', 'No se puede eliminar: la promoción ya fue utilizada en ventas. Se inactivó en su lugar.');
    }

    // ✅ Si no tiene ventas, ahora sí se elimina completamente
    $promocion->productos()->detach();
    $promocion->delete();

    return redirect()->route('promociones.index')->with('success', 'Promoción eliminada.');
}

    public function toggle(Promocion $promocion)
    {
        $promocion->activo = ! (bool) $promocion->activo;
        $promocion->save();

        return redirect()->route('promociones.index')->with('success', 'Estado actualizado.');
    }
}
