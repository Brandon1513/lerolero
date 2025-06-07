<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Ventas Registradas') }}
        </h2>
    </x-slot>

    <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Botón Registrar -->
        <div class="mb-4">
            <a href="{{ route('ventas.create') }}" class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                Registrar Venta
            </a>
        </div>

        <!-- Filtros -->
        <form method="GET" action="{{ route('ventas.index') }}" class="flex flex-wrap items-end gap-4 mb-6">
            <!-- Vendedor -->
            <div>
                <x-input-label for="vendedor_id" value="Vendedor" />
                <select name="vendedor_id" id="vendedor_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    <option value="">Todos</option>
                    @foreach ($vendedores as $v)
                        <option value="{{ $v->id }}" {{ request('vendedor_id') == $v->id ? 'selected' : '' }}>
                            {{ $v->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Cliente -->
            <div>
                <x-input-label for="cliente_id" value="Cliente" />
                <select name="cliente_id" id="cliente_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    <option value="">Todos</option>
                    @foreach ($clientes as $c)
                        <option value="{{ $c->id }}" {{ request('cliente_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha desde -->
            <div>
                <x-input-label for="desde" value="Desde" />
                <x-text-input type="date" name="desde" id="desde" value="{{ request('desde') }}" class="block w-full mt-1" />
            </div>

            <!-- Fecha hasta -->
            <div>
                <x-input-label for="hasta" value="Hasta" />
                <x-text-input type="date" name="hasta" id="hasta" value="{{ request('hasta') }}" class="block w-full mt-1" />
            </div>

            <!-- Botones -->
            <div class="flex gap-2 mt-1">
                <x-primary-button class="h-[42px]">Filtrar</x-primary-button>
                <a href="{{ route('ventas.index') }}"
                   class="px-4 py-2 text-sm text-gray-800 bg-gray-300 rounded-md hover:bg-gray-400">
                    Limpiar
                </a>
            </div>
        </form>

        <!-- Tabla de Ventas -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <table class="w-full text-sm border border-collapse">
                <thead class="text-left bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Fecha</th>
                        <th class="px-4 py-2 border">Cliente</th>
                        <th class="px-4 py-2 border">Vendedor</th>
                        <th class="px-4 py-2 border">Total</th>
                        <th class="px-4 py-2 border">Observaciones</th>
                        <th class="px-4 py-2 text-center border">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $venta->id }}</td>
                            <td class="px-4 py-2 border">{{ $venta->fecha }}</td>
                            <td class="px-4 py-2 border">{{ $venta->cliente->nombre }}</td>
                            <td class="px-4 py-2 border">{{ $venta->vendedor->name }}</td>
                            <td class="px-4 py-2 border">${{ number_format($venta->total, 2) }}</td>
                            <td class="px-4 py-2 border">{{ $venta->observaciones ?? '-' }}</td>
                            <td class="px-4 py-2 text-center border">
                                <a href="{{ route('ventas.show', $venta) }}" class="text-blue-600 hover:underline">
                                    Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No hay ventas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $ventas->withQueryString()->links() }}
        </div>
    </div>
</x-app-layout>
