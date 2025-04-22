<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cliente;
use App\Models\NivelPrecio;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
{
    $clientes = Cliente::query();

    // Filtro por nombre
    if ($request->filled('nombre')) {
        $clientes->where('nombre', 'like', '%' . $request->nombre . '%');
    }

    // Filtro por estado
    if ($request->estado === 'activo') {
        $clientes->where('activo', true);
    } elseif ($request->estado === 'inactivo') {
        $clientes->where('activo', false);
    }

    // Filtro por vendedor
    if ($request->filled('asignado_a')) {
        $clientes->where('asignado_a', $request->asignado_a);
    }

    $clientes = $clientes->with(['asignadoA', 'nivelPrecio'])->paginate(10)->withQueryString();


    // Lista de vendedores para el filtro
    $vendedores = \App\Models\User::role('vendedor')->get();

    return view('clientes.index', compact('clientes', 'vendedores'));
}




public function create()
{
    $vendedores = User::role('vendedor')->get();
    $niveles = NivelPrecio::all();
    return view('clientes.create', compact('vendedores', 'niveles'));
}

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'asignado_a' => 'nullable|exists:users,id',
            'nivel_precio_id' => 'nullable|exists:niveles_precio,id',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'dias_visita' => 'nullable|array',
            'dias_visita.*' => 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',

        ]);

        Cliente::create($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente agregado correctamente.');
    }

    public function edit(Cliente $cliente)
    {
        $vendedores = User::role('vendedor')->get();
        $niveles = NivelPrecio::all();
        return view('clientes.edit', compact('cliente', 'vendedores', 'niveles'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'asignado_a' => 'nullable|exists:users,id',
            'nivel_precio_id' => 'nullable|exists:niveles_precio,id',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'dias_visita' => 'nullable|array',
            'dias_visita.*' => 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo',

        ]);

        $cliente->update($request->all());

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }
//activar o desactivar cliente
    public function toggleActivo(Cliente $cliente)
    {
    $cliente->activo = !$cliente->activo;
    $cliente->save();

    $mensaje = $cliente->activo ? 'Cliente activado correctamente.' : 'Cliente inactivado correctamente.';

    return redirect()->route('clientes.index')->with('success', $mensaje);
    }

}
