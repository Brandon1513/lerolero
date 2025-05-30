<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Models\NivelPrecio;
use App\Models\ProductoNivelPrecio;
use Illuminate\Http\Request;
use App\Models\Almacen;
use App\Models\Inventario;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'unidadMedida'])->get();
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::where('activo', true)->get();
        $unidades = UnidadMedida::where('activo', true)->get();
        $niveles = NivelPrecio::all(); // aquí es donde lo agregas
        return view('productos.create', compact('categorias', 'unidades', 'niveles'));
    }

    public function store(Request $request)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'marca' => 'nullable|string|max:255',
        'categoria_id' => 'required|exists:categorias,id',
        'unidad_medida_id' => 'required|exists:unidades_medida,id',
        'precio' => 'required|numeric|min:0',
        'fecha_caducidad' => 'nullable|date',
        'cantidad' => 'required|numeric|min:0',
        'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $imagePath = null;
    if ($request->hasFile('imagen')) {
        $imagePath = $request->file('imagen')->store('productos', 'public');
    }

    $data = $request->only([
        'nombre',
        'marca',
        'categoria_id',
        'unidad_medida_id',
        'precio',
        'fecha_caducidad',
        'cantidad',
    ]);

    $data['imagen'] = $imagePath;

    $producto = Producto::create($data);

    Inventario::create([
        'producto_id' => $producto->id,
        'almacen_id' => 1,
        'cantidad' => $producto->cantidad,
    ]);

    return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
}


    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('activo', true)->get();
        $unidades = UnidadMedida::where('activo', true)->get();
        return view('productos.edit', compact('producto', 'categorias', 'unidades'));
    }

    public function update(Request $request, Producto $producto)
{
    $request->validate([
        'nombre' => 'required|string|max:255',
        'marca' => 'nullable|string|max:255',
        'categoria_id' => 'required|exists:categorias,id',
        'unidad_medida_id' => 'required|exists:unidades_medida,id',
        'precio' => 'required|numeric|min:0',
        'fecha_caducidad' => 'nullable|date',
        'cantidad' => 'required|numeric|min:0',
        'imagen' => 'nullable|image|max:2048',
    ]);

    // Manejar la imagen si se subió una nueva
    if ($request->hasFile('imagen')) {
        $imagePath = $request->file('imagen')->store('productos', 'public');
        $producto->imagen = $imagePath;
    }

    // Actualizar el resto de los campos
    $producto->fill($request->only([
        'nombre',
        'marca',
        'categoria_id',
        'unidad_medida_id',
        'precio',
        'fecha_caducidad',
        'cantidad',
    ]));

    $producto->save();

    // Actualizar el inventario en el almacén general (ID 1)
    $inventario = Inventario::firstOrNew([
        'producto_id' => $producto->id,
        'almacen_id' => 1,
    ]);

    $inventario->cantidad = $producto->cantidad;
    $inventario->save();

    return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
}


    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}
