<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Producto;
use Illuminate\Http\Request;

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

        // Correcto: clave = producto_id (id), valor = cantidad
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
        $promocion->productos()->detach();
        $promocion->delete();

        return redirect()->route('promociones.index')->with('success', 'Promoción eliminada.');
    }
    public function toggle(Promocion $promocion)
    {
        $promocion->activo = !$promocion->activo;
        $promocion->save();

        return redirect()->route('promociones.index')->with('success', 'Estado actualizado.');
    }

}
