<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Cliente;
use App\Models\NivelPrecio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class ClienteController extends Controller
{
   public function index(Request $request)
    {
        $clientesQuery = Cliente::query();

        // Filtro por nombre
        if ($request->filled('nombre')) {
            $clientesQuery->where('nombre', 'like', '%' . $request->nombre . '%');
        }

        // Filtro por estado
        if ($request->estado === 'activo') {
            $clientesQuery->where('activo', true);
        } elseif ($request->estado === 'inactivo') {
            $clientesQuery->where('activo', false);
        }

        // Filtro por vendedor
        if ($request->filled('asignado_a')) {
            $clientesQuery->where('asignado_a', $request->asignado_a);
        }

        $clientes = $clientesQuery
            ->with(['asignadoA', 'nivelPrecio'])
            ->paginate(10)
            ->withQueryString();

        // ✅ Marcar si se puede eliminar o no (historial)
        $clientes->getCollection()->transform(function ($c) {
            $tieneVentas = DB::getSchemaBuilder()->hasTable('ventas')
                ? DB::table('ventas')->where('cliente_id', $c->id)->exists()
                : false;

            $tieneVisitas = DB::getSchemaBuilder()->hasTable('visitas_clientes')
                ? DB::table('visitas_clientes')->where('cliente_id', $c->id)->exists()
                : false;

            // Agrega aquí más tablas si aplica (por ejemplo, devoluciones, notas_credito, etc.)

            $tieneHistorial = $tieneVentas || $tieneVisitas;

            $c->puede_eliminar = ! $tieneHistorial;
            $c->tiene_historial = $tieneHistorial;

            return $c;
        });

        // Lista de vendedores para el filtro
        $vendedores = User::role('vendedor')->get();

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
        // ✅ Regla: NO borrar si tiene historial. Mejor inactivar.
        $razones = [];
        $tieneHistorial = false;

        if (DB::getSchemaBuilder()->hasTable('ventas')) {
            if (DB::table('ventas')->where('cliente_id', $cliente->id)->exists()) {
                $tieneHistorial = true;
                $razones[] = 'ventas';
            }
        }

        if (DB::getSchemaBuilder()->hasTable('visitas_clientes')) {
            if (DB::table('visitas_clientes')->where('cliente_id', $cliente->id)->exists()) {
                $tieneHistorial = true;
                $razones[] = 'visitas';
            }
        }

        if ($tieneHistorial) {
            $cliente->activo = false;
            $cliente->save();

            return redirect()->route('clientes.index')
                ->with('error', 'No se puede eliminar: el cliente ya tiene historial (' . implode(', ', $razones) . '). Se inactivó en su lugar.');
        }

        // Si NO tiene historial, ahora sí borramos
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
