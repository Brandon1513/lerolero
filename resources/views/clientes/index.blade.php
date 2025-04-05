<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Lista de Clientes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            
            <!-- Mensaje de éxito -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Botón Agregar -->
                    <div class="mb-4">
                        <a href="{{ route('clientes.create') }}" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-700">
                            Agregar Cliente
                        </a>
                    </div>

                    <!-- Filtros -->
                    <form method="GET" action="{{ route('clientes.index') }}" class="flex flex-wrap items-end gap-4 mb-4">
                        <!-- Buscar por nombre -->
                        <div>
                            <x-input-label for="nombre" :value="__('Nombre')" />
                            <x-text-input id="nombre" name="nombre" type="text" class="block w-full mt-1"
                                value="{{ request('nombre') }}" placeholder="Buscar por nombre..." />
                        </div>

                        <!-- Filtrar por estado -->
                        <div>
                            <x-input-label for="estado" :value="__('Estado')" />
                            <select name="estado" id="estado" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Todos --</option>
                                <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                                <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <!-- Filtrar por vendedor asignado -->
                        <div>
                            <x-input-label for="asignado_a" :value="__('Vendedor')" />
                            <select name="asignado_a" id="asignado_a" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Todos --</option>
                                @foreach ($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}" {{ request('asignado_a') == $vendedor->id ? 'selected' : '' }}>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-end gap-2">
                            <x-primary-button class="h-[42px]">
                                {{ __('Filtrar') }}
                            </x-primary-button>

                            <a href="{{ route('clientes.index') }}"
                               class="h-[42px] px-4 py-2 text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md flex items-center">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <!-- Contador de resultados -->
                    @if ($clientes->count() > 0)
                        <p class="mb-2 text-sm text-gray-600">
                            Mostrando <span class="font-semibold">{{ $clientes->count() }}</span> cliente{{ $clientes->count() > 1 ? 's' : '' }}.
                        </p>
                    @else
                        <p class="mb-2 text-sm text-red-600">
                            No se encontraron clientes con los filtros aplicados.
                        </p>
                    @endif

                    <!-- Tabla de clientes -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border border-collapse border-gray-200">
                            <thead>
                                <tr class="text-left text-gray-700 bg-gray-100">
                                    <th class="px-4 py-2 border">Nombre</th>
                                    <th class="px-4 py-2 border">Dirección</th>
                                    <th class="px-4 py-2 border">Teléfono</th>
                                    <th class="px-4 py-2 border">Asignado a</th>
                                    <th class="px-4 py-2 text-center border">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientes as $cliente)
                                    <tr class="hover:bg-gray-50">
                                        <!-- Nombre -->
                                        <td class="px-4 py-2 font-medium border">{{ $cliente->nombre }}</td>

                                        <!-- Dirección completa -->
                                        <td class="px-4 py-2 text-gray-700 border">
                                            @if ($cliente->calle || $cliente->colonia || $cliente->codigo_postal)
                                                <div>{{ $cliente->calle }}</div>
                                                <div>{{ $cliente->colonia }}, CP {{ $cliente->codigo_postal }}</div>
                                                <div>{{ $cliente->municipio }}, {{ $cliente->estado }}</div>
                                            @else
                                                <span class="text-gray-400">No especificada</span>
                                            @endif
                                        </td>

                                        <!-- Teléfono -->
                                        <td class="px-4 py-2 border">{{ $cliente->telefono }}</td>

                                        <!-- Asignado a -->
                                        <td class="px-4 py-2 border">
                                            {{ $cliente->asignadoA ? $cliente->asignadoA->name : 'No asignado' }}
                                        </td>

                                        <!-- Acciones -->
                                        <td class="px-4 py-2 text-center border">
                                            <div class="flex flex-wrap justify-center gap-2">
                                                <!-- Editar -->
                                                <a href="{{ route('clientes.edit', $cliente) }}"
                                                    class="px-3 py-1 text-white bg-yellow-500 rounded-md hover:bg-yellow-700">
                                                    Editar
                                                </a>

                                                <!-- Eliminar -->
                                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                                    onsubmit="return confirm('¿Eliminar cliente?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 text-white bg-red-500 rounded-md hover:bg-red-700">
                                                        Eliminar
                                                    </button>
                                                </form>

                                                <!-- Activar/Inactivar -->
                                                <form action="{{ route('clientes.toggle', $cliente) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                        class="px-3 py-1 text-white rounded-md
                                                            {{ $cliente->activo ? 'bg-gray-500 hover:bg-gray-700' : 'bg-green-500 hover:bg-green-700' }}">
                                                        {{ $cliente->activo ? 'Inactivar' : 'Activar' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <!-- Paginación -->
                        <div class="mt-4">
                            {{ $clientes->links() }}
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
