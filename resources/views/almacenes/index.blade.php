<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Lista de Almacenes') }}
        </h2>
    </x-slot>

    <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <!-- Botón Crear -->
        <div class="mb-4">
            <a href="{{ route('almacenes.create') }}"
               class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
               Crear Almacén
            </a>
        </div>
        <!-- Filtros -->
        <form method="GET" class="flex flex-wrap items-end gap-4 mb-4">
            <!-- Buscar por nombre -->
            <div>
                <x-input-label for="buscar" value="Buscar por Nombre" />
                <x-text-input id="buscar" name="buscar" type="text" value="{{ request('buscar') }}" class="block w-full mt-1" />
            </div>

            <!-- Filtrar por tipo -->
            <div>
                <x-input-label for="tipo" value="Tipo de Almacén" />
                <select name="tipo" id="tipo" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Todos --</option>
                    <option value="general" {{ request('tipo') == 'general' ? 'selected' : '' }}>General</option>
                    <option value="vendedor" {{ request('tipo') == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                </select>
            </div>

            <!-- Botones -->
            <div class="flex items-end gap-2">
                <x-primary-button class="h-[42px]">
                    {{ __('Filtrar') }}
                </x-primary-button>

                <a href="{{ route('almacenes.index') }}"
                class="h-[42px] px-4 py-2 text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md flex items-center">
                    Limpiar
                </a>
            </div>
        </form>


        <!-- Tabla -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full text-sm border border-collapse">
                <thead class="text-left bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Nombre</th>
                        <th class="px-4 py-2 border">Tipo</th>
                        <th class="px-4 py-2 border">Ubicación</th>
                        <th class="px-4 py-2 border">Usuario Asignado</th>
                        <th class="px-4 py-2 text-center border">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($almacenes as $almacen)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $almacen->nombre }}</td>
                            <td class="px-4 py-2 capitalize border">{{ $almacen->tipo }}</td>
                            <td class="px-4 py-2 border">{{ $almacen->ubicacion }}</td>
                            <td class="px-4 py-2 border">
                                {{ $almacen->usuario?->name ?? 'No asignado' }}
                            </td>
                            <td class="px-4 py-2 text-center border">
                                <div class="flex flex-wrap justify-center gap-2">
                                    <!-- Editar -->
                                    <a href="{{ route('almacenes.edit', $almacen) }}"
                                       class="px-3 py-1 text-white bg-yellow-500 rounded hover:bg-yellow-700">
                                        Editar
                                    </a>

                                    <!-- Activar/Inactivar -->
                                    <form action="{{ route('almacenes.toggle', $almacen) }}" method="POST"
                                          onsubmit="return confirm('¿Estás seguro de cambiar el estado del almacén?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1 text-white rounded
                                                {{ $almacen->activo ? 'bg-gray-500 hover:bg-gray-700' : 'bg-green-500 hover:bg-green-700' }}">
                                            {{ $almacen->activo ? 'Inactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                No hay almacenes registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <!-- Paginación -->
            <div class="mt-4">
                {{ $almacenes->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
