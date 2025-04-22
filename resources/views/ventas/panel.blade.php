<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Panel de Ventas por Vendedor</h2>
    </x-slot>

    <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <form method="GET" class="flex flex-wrap gap-4 p-6 mb-6 bg-white rounded shadow">
            <div>
                <x-input-label for="vendedor_id" value="Vendedor" />
                <select name="vendedor_id" id="vendedor_id" class="block w-full mt-1">
                    <option value="">-- Todos --</option>
                    @foreach ($vendedores as $v)
                        <option value="{{ $v->id }}" {{ request('vendedor_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="fecha_inicio" value="Desde" />
                <x-text-input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ request('fecha_inicio') }}" />
            </div>

            <div>
                <x-input-label for="fecha_fin" value="Hasta" />
                <x-text-input type="date" name="fecha_fin" id="fecha_fin" value="{{ request('fecha_fin') }}" />
            </div>

            <div class="flex items-end">
                <x-primary-button class="mt-2">Filtrar</x-primary-button>
            </div>
        </form>

        <div class="p-4 mb-4 text-gray-800 bg-white rounded shadow">
            <strong>Total general:</strong> ${{ number_format($totalGeneral, 2) }}
        </div>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="w-full text-sm border border-collapse">
                <thead class="text-left bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Fecha</th>
                        <th class="px-4 py-2 border">Cliente</th>
                        <th class="px-4 py-2 border">Vendedor</th>
                        <th class="px-4 py-2 border">Total</th>
                        <th class="px-4 py-2 text-center border">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $venta->fecha }}</td>
                            <td class="px-4 py-2 border">{{ $venta->cliente->nombre }}</td>
                            <td class="px-4 py-2 border">{{ $venta->vendedor->name }}</td>
                            <td class="px-4 py-2 border">${{ number_format($venta->total, 2) }}</td>
                            <td class="px-4 py-2 text-center border">
                                <a href="{{ route('ventas.show', $venta) }}" class="text-blue-600 hover:underline">Ver detalle</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $ventas->links() }}
        </div>
    </div>
</x-app-layout>
