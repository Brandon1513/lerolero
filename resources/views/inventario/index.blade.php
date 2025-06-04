<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Inventario General</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Filtros -->
        <form method="GET" class="flex flex-wrap gap-4 mb-6">
            <div>
                <x-input-label for="producto_id" value="Producto" />
                <select name="producto_id" id="producto_id" class="block w-full mt-1 border-gray-300 rounded-md">
                    <option value="">-- Todos --</option>
                    @foreach ($productos as $prod)
                        <option value="{{ $prod->id }}" {{ request('producto_id') == $prod->id ? 'selected' : '' }}>
                            {{ $prod->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="almacen_id" value="Almacén" />
                <select name="almacen_id" id="almacen_id" class="block w-full mt-1 border-gray-300 rounded-md">
                    <option value="">-- Todos --</option>
                    @foreach ($almacenes as $alm)
                        <option value="{{ $alm->id }}" {{ request('almacen_id') == $alm->id ? 'selected' : '' }}>
                            {{ $alm->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <x-primary-button>Filtrar</x-primary-button>
            </div>
        </form>

        <!-- Tabla -->
        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="w-full text-sm border border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Producto</th>
                        <th class="px-4 py-2 border">Fecha de Caducidad</th>
                        <th class="px-4 py-2 border">Cantidad</th>
                        <th class="px-4 py-2 border">Lote</th>
                        <th class="px-4 py-2 border">Almacén</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inventarios as $inv)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $inv->producto->nombre }}</td>
                            <td class="px-4 py-2 border">
                                {{ $inv->fecha_caducidad ? \Carbon\Carbon::parse($inv->fecha_caducidad)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="px-4 py-2 border text-center">{{ $inv->cantidad }}</td>
                            <td class="px-4 py-2 border">{{ $inv->lote ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $inv->almacen->nombre }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-gray-500">No hay resultados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $inventarios->links() }}
        </div>
    </div>
</x-app-layout>
