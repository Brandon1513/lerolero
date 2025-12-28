<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Models\NivelPrecio;
use App\Models\ProductoNivelPrecio; // <-- importa el modelo pivot
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DetalleTraslado;


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
        $unidades   = UnidadMedida::where('activo', true)->get();
        $niveles    = NivelPrecio::all();

        return view('productos.create', compact('categorias', 'unidades', 'niveles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'            => 'required|string|max:255',
            'marca'             => 'nullable|string|max:255',
            'categoria_id'      => 'required|exists:categorias,id',
            'unidad_medida_id'  => 'required|exists:unidades_medida,id',
            'precio'            => 'required|numeric|min:0',
            'imagen'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            // precios por nivel (vienen como niveles[<nivel_id>] => precio)
            'niveles'           => 'nullable|array',
            'niveles.*'         => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Imagen
            $imagePath = null;
            if ($request->hasFile('imagen')) {
                $imagePath = $request->file('imagen')->store('productos', 'public');
            }

            // Producto base
            $producto = Producto::create([
                'nombre'           => $request->nombre,
                'marca'            => $request->marca,
                'categoria_id'     => $request->categoria_id,
                'unidad_medida_id' => $request->unidad_medida_id,
                'precio'           => $request->precio, // precio base
                'imagen'           => $imagePath,
            ]);

            // Precios por nivel
            $niveles = $request->input('niveles', []); // [nivel_id => precio]
            foreach ($niveles as $nivelId => $precio) {
                if ($precio === null || $precio === '') continue;

                ProductoNivelPrecio::updateOrCreate(
                    ['producto_id' => $producto->id, 'nivel_precio_id' => (int) $nivelId],
                    ['precio' => (float) $precio]
                );
            }

            DB::commit();
            return redirect()->route('productos.index')
                             ->with('success', 'Producto creado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Error al guardar el producto: '.$e->getMessage())
                         ->withInput();
        }
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::where('activo', true)->get();
        $unidades   = UnidadMedida::where('activo', true)->get();
        $niveles    = NivelPrecio::all();

        // para precargar valores en el form
        $producto->load('preciosNivel'); // relación hasMany al pivot

        return view('productos.edit', compact('producto', 'categorias', 'unidades', 'niveles'));
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre'            => 'required|string|max:255',
            'marca'             => 'nullable|string|max:255',
            'categoria_id'      => 'required|exists:categorias,id',
            'unidad_medida_id'  => 'required|exists:unidades_medida,id',
            'precio'            => 'required|numeric|min:0',
            'imagen'            => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            'niveles'           => 'nullable|array',
            'niveles.*'         => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Imagen
            if ($request->hasFile('imagen')) {
                $imagePath = $request->file('imagen')->store('productos', 'public');
                $producto->imagen = $imagePath;
            }

            // Datos base
            $producto->fill($request->only([
                'nombre',
                'marca',
                'categoria_id',
                'unidad_medida_id',
                'precio',
            ]));
            $producto->save();

            // Sincronizar precios por nivel
            $niveles = $request->input('niveles', []); // [nivel_id => precio]
            $nivelIdsGuardados = [];

            foreach ($niveles as $nivelId => $precio) {
                if ($precio === null || $precio === '') continue;

                ProductoNivelPrecio::updateOrCreate(
                    ['producto_id' => $producto->id, 'nivel_precio_id' => (int) $nivelId],
                    ['precio' => (float) $precio]
                );

                $nivelIdsGuardados[] = (int) $nivelId;
            }

            // Eliminar precios de niveles que ya no están en el formulario
            if (!empty($nivelIdsGuardados)) {
                ProductoNivelPrecio::where('producto_id', $producto->id)
                    ->whereNotIn('nivel_precio_id', $nivelIdsGuardados)
                    ->delete();
            } else {
                // Si no enviaron ninguno, eliminamos todos los existentes
                ProductoNivelPrecio::where('producto_id', $producto->id)->delete();
            }

            DB::commit();
            return redirect()->route('productos.index')
                             ->with('success', 'Producto actualizado correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors('Error al actualizar el producto: '.$e->getMessage())
                         ->withInput();
        }
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }
}
