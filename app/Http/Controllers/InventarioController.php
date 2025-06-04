<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use App\Models\Producto;
use App\Models\Inventario;

use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Muestra el inventario real por almacén y producto (con lotes).
     */
    public function index(Request $request)
    {
        $query = Inventario::with(['producto', 'almacen']);


        if ($request->filled('producto_id')) {
            $query->where('producto_id', $request->producto_id);
        }

        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }

        $inventarios = $query->orderBy('fecha_caducidad')->paginate(10)->withQueryString();
        $almacenes = Almacen::orderBy('nombre')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('inventario.index', compact('inventarios', 'almacenes', 'productos'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
