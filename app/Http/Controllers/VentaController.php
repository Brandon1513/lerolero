<?php

namespace App\Http\Controllers;

use DB;
use App\Models\User;
use App\Models\Venta;
use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\DetalleVenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    // Mostrar todas las ventas
    public function index(Request $request)
    {
        $ventas = Venta::query()
            ->with(['cliente', 'vendedor'])
            ->when($request->vendedor_id, fn($q) =>
                $q->where('vendedor_id', $request->vendedor_id))
            ->when($request->cliente_id, fn($q) =>
                $q->where('cliente_id', $request->cliente_id))
            ->when($request->desde, fn($q) =>
                $q->whereDate('fecha', '>=', $request->desde))
            ->when($request->hasta, fn($q) =>
                $q->whereDate('fecha', '<=', $request->hasta))
            ->orderByDesc('fecha')
            ->paginate(10);

        $vendedores = User::role('vendedor')->get();
        $clientes = Cliente::orderBy('nombre')->get();

        return view('ventas.index', compact('ventas', 'vendedores', 'clientes'));
    }


    // Formulario para crear nueva venta
    public function create()
    {
        $clientes = Cliente::where('activo', true)->get();
        $productos = Producto::where('activo', true)->get();

        return view('ventas.create', compact('clientes', 'productos'));
    }

    // Guardar venta en base de datos
    // Guardar venta en base de datos
public function store(Request $request)
{
    $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'fecha' => 'required|date',
        'productos' => 'required|array',
        'productos.*.producto_id' => 'required|exists:productos,id',
        'productos.*.cantidad' => 'required|numeric|min:1',
        'productos.*.precio_unitario' => 'required|numeric|min:0',
        'productos.*.es_cambio' => 'nullable|boolean',
        'productos.*.motivo_cambio' => 'nullable|string|max:255',
        'observaciones' => 'nullable|string|max:500',
    ]);

    $vendedor = Auth::user();

    $almacenVendedor = Almacen::where('tipo', 'vendedor')
                      ->where('user_id', $vendedor->id)
                      ->first();

    if (!$almacenVendedor) {
        return back()->withErrors('No tienes un almacén asignado.');
    }

    // Calculamos el total SOLO de productos que no sean cambio
    $total = collect($request->productos)->sum(function ($p) {
        return (!empty($p['es_cambio']) && $p['es_cambio']) ? 0 : ($p['cantidad'] * $p['precio_unitario']);
    });

    $venta = Venta::create([
        'cliente_id' => $request->cliente_id,
        'vendedor_id' => $vendedor->id,
        'fecha' => $request->fecha,
        'total' => $total,
        'observaciones' => $request->observaciones,
    ]);

    foreach ($request->productos as $producto) {
        $esCambio = !empty($producto['es_cambio']) ? true : false;
        $motivoCambio = $producto['motivo_cambio'] ?? null;

        DetalleVenta::create([
            'venta_id' => $venta->id,
            'producto_id' => $producto['producto_id'],
            'cantidad' => $producto['cantidad'],
            'precio_unitario' => $producto['precio_unitario'],
            'subtotal' => $esCambio ? 0 : ($producto['cantidad'] * $producto['precio_unitario']),
            'almacen_id' => $almacenVendedor->id,
            'es_cambio' => $esCambio,
            'motivo_cambio' => $motivoCambio,
        ]);

        if ($esCambio) {
            // 👉 Los productos de cambio van al almacén de rechazos (id=3)
            \App\Models\Inventario::updateOrCreate(
                [
                    'producto_id' => $producto['producto_id'],
                    'almacen_id' => 3
                ],
                [
                    'cantidad' => DB::raw('cantidad + ' . $producto['cantidad'])
                ]
            );
        } else {
            // 👉 Los productos vendidos se descuentan del almacén del vendedor
            \App\Models\Inventario::where('producto_id', $producto['producto_id'])
                ->where('almacen_id', $almacenVendedor->id)
                ->decrement('cantidad', $producto['cantidad']);
        }
    }

    return redirect()->route('ventas.index')->with('success', 'Venta registrada correctamente.');
}


    public function show(Venta $venta)
{
    // Carga todas las relaciones necesarias de la venta:
    $venta->load([
        'cliente',
        'vendedor',
        'detalles.producto',
        'detalles.almacen',
        'rechazos.producto',
        // Asegúrate de que esta tabla 'rechazos_temporales' tenga la columna almacen_id
        'rechazos.almacen',
        'promociones.promocion.productos', // 👈 para el desglose
    ]);

    return view('ventas.show', compact('venta'));
}


    public function panel(Request $request)
    {
        $vendedores = \App\Models\User::role('vendedor')->get();

        $ventas = Venta::with('cliente', 'vendedor')
            ->when($request->vendedor_id, fn($q) => $q->where('vendedor_id', $request->vendedor_id))
            ->when($request->fecha_inicio, fn($q) =>
                $q->whereDate('fecha', '>=', $request->fecha_inicio))
            ->when($request->fecha_fin, fn($q) =>
                $q->whereDate('fecha', '<=', $request->fecha_fin))
            ->orderByDesc('fecha')
            ->paginate(10);

        $totalGeneral = $ventas->sum('total');

        return view('ventas.panel', compact('ventas', 'vendedores', 'totalGeneral'));
    }

}
