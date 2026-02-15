<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Models\NivelPrecio;
use App\Models\ProductoNivelPrecio; // <-- importa el modelo pivot
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ProductoController extends Controller
{
    public function index(Request $request)
{
    // ── Stats globales (independientes del filtro) ────────────
    $statsTotal        = Producto::count();
    $statsActivos      = Producto::where('activo', true)->count();
    $statsInactivos    = Producto::where('activo', false)->count();
    $statsConMovimientos = DB::table('detalle_ventas')
        ->distinct('producto_id')->count('producto_id');

    // ── Query base con filtros ────────────────────────────────
    $query = Producto::with(['categoria', 'unidadMedida'])
        ->orderBy('nombre');

    // Filtro: búsqueda por nombre o marca
    if ($buscar = $request->input('buscar')) {
        $query->where(function ($q) use ($buscar) {
            $q->where('nombre', 'like', "%{$buscar}%")
              ->orWhere('marca',  'like', "%{$buscar}%");
        });
    }

    // Filtro: categoría
    if ($catId = $request->input('categoria_id')) {
        $query->where('categoria_id', $catId);
    }

    // Filtro: estado activo/inactivo
    if ($estado = $request->input('estado')) {
        $query->where('activo', $estado === 'activo');
    }

    // ── Paginado (15 por página, mantiene query params) ───────
    $productos = $query->paginate(15)->withQueryString();

    // ── Enriquecer con flags de movimientos (por lote) ───────
    // Solo consultamos los ids de la página actual
    $idsEnPagina = $productos->pluck('id')->toArray();

    $idsConVentas = DB::table('detalle_ventas')
        ->whereIn('producto_id', $idsEnPagina)
        ->distinct()->pluck('producto_id')->flip()->toArray();

    $idsConTraslados = DB::table('detalle_traslado')
        ->whereIn('producto_id', $idsEnPagina)
        ->distinct()->pluck('producto_id')->flip()->toArray();

    $idsConInventario = DB::table('inventario_almacen')
        ->whereIn('producto_id', $idsEnPagina)
        ->distinct()->pluck('producto_id')->flip()->toArray();

    $productos->getCollection()->transform(function ($p) use (
        $idsConVentas, $idsConTraslados, $idsConInventario
    ) {
        $tieneMovimientos = isset($idsConVentas[$p->id])
            || isset($idsConTraslados[$p->id])
            || isset($idsConInventario[$p->id]);

        $p->tiene_movimientos = $tieneMovimientos;
        $p->puede_eliminar    = !$tieneMovimientos;

        return $p;
    });

    // ── Categorías para el filtro ─────────────────────────────
    $categorias = \App\Models\Categoria::where('activo', true)
        ->orderBy('nombre')->get();

    return view('productos.index', compact(
        'productos',
        'categorias',
        'statsTotal',
        'statsActivos',
        'statsInactivos',
        'statsConMovimientos'
    ));
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
        // Si ya hay movimientos, NO permitir borrado. Solo inactivar.
        $tieneMovimientos = false;
        $razones = [];

        // Traslados
        if (DB::getSchemaBuilder()->hasTable('detalle_traslados')) {
            $existe = DB::table('detalle_traslados')->where('producto_id', $producto->id)->exists();
            if ($existe) { $tieneMovimientos = true; $razones[] = 'traslados'; }
        }

        // Ventas
        if (DB::getSchemaBuilder()->hasTable('detalle_ventas')) {
            $existe = DB::table('detalle_ventas')->where('producto_id', $producto->id)->exists();
            if ($existe) { $tieneMovimientos = true; $razones[] = 'ventas'; }
        }

        // Producciones
        if (DB::getSchemaBuilder()->hasTable('producciones')) {
            $existe = DB::table('producciones')->where('producto_id', $producto->id)->exists();
            if ($existe) { $tieneMovimientos = true; $razones[] = 'producciones'; }
        }

        // Inventario (si existe, normalmente también bloquea borrado)
        if (DB::getSchemaBuilder()->hasTable('inventario_almacen')) {
            $existe = DB::table('inventario_almacen')->where('producto_id', $producto->id)->exists();
            if ($existe) { $tieneMovimientos = true; $razones[] = 'inventario'; }
        }

        // Precios por nivel (pivot)
        if (DB::getSchemaBuilder()->hasTable('producto_nivel_precio')) {
            $existe = DB::table('producto_nivel_precio')->where('producto_id', $producto->id)->exists();
            if ($existe) { $tieneMovimientos = true; $razones[] = 'precios por nivel'; }
        }

        if ($tieneMovimientos) {
            // Mejor práctica: inactivar en vez de borrar
            $producto->activo = false;
            $producto->save();

            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar: el producto ya tiene ' . implode(', ', $razones) . '. Se inactivó en su lugar.');
        }

        // Si NO tiene movimientos, ahora sí puedes borrar (opcional)
        DB::transaction(function () use ($producto) {
            // Limpia pivots si quieres
            if (DB::getSchemaBuilder()->hasTable('producto_nivel_precio')) {
                DB::table('producto_nivel_precio')->where('producto_id', $producto->id)->delete();
            }

            $producto->delete();
        });

        return redirect()->route('productos.index')->with('success', 'Producto eliminado correctamente.');
    }


    public function toggle(Producto $producto)
    {
        $producto->activo = ! (bool) $producto->activo;
        $producto->save();

        return back()->with('success', 'Producto ' . ($producto->activo ? 'activado' : 'inactivado') . ' correctamente.');
    }

}
