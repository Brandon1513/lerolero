<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Historial de Traslados</h2>
    </x-slot>

    <div class="max-w-6xl py-12 mx-auto sm:px-6 lg:px-8">

        <!-- Mensaje de éxito -->
        @if (session('success'))
            <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Botón Crear -->
        <div class="mb-6">
            <a href="{{ route('traslados.create') }}"
               class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                Crear Traslado
            </a>
        </div>

        <!-- Filtros -->
        <form method="GET" class="flex flex-wrap items-end gap-4 mb-6">
            <!-- Fecha Inicio -->
            <div>
                <x-input-label for="fecha_inicio" value="Fecha Inicio" />
                <x-text-input type="date" name="fecha_inicio" id="fecha_inicio" class="block w-full mt-1"
                    value="{{ request('fecha_inicio') }}" />
            </div>

            <!-- Fecha Fin -->
            <div>
                <x-input-label for="fecha_fin" value="Fecha Fin" />
                <x-text-input type="date" name="fecha_fin" id="fecha_fin" class="block w-full mt-1"
                    value="{{ request('fecha_fin') }}" />
            </div>

            <!-- Almacén Destino -->
            <div>
                <x-input-label for="destino_id" value="Destino" />
                <select name="destino_id" id="destino_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Todos --</option>
                    @foreach ($almacenes as $almacen)
                        <option value="{{ $almacen->id }}" {{ request('destino_id') == $almacen->id ? 'selected' : '' }}>
                            {{ $almacen->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Botones -->
            <div class="flex items-end gap-2">
                <x-primary-button class="h-[42px]">
                    Filtrar
                </x-primary-button>

                <a href="{{ route('traslados.index') }}"
                   class="h-[42px] px-4 py-2 text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md flex items-center">
                    Limpiar
                </a>
            </div>
        </form>

        <!-- Tabla -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <table class="w-full text-sm border border-collapse">
                <thead class="text-left bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Fecha</th>
                        <th class="px-4 py-2 border">Origen</th>
                        <th class="px-4 py-2 border">Destino</th>
                        <th class="px-4 py-2 border">Observaciones</th>
                        <th class="px-4 py-2 text-center border">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($traslados as $traslado)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $traslado->fecha }}</td>
                            <td class="px-4 py-2 border">{{ $traslado->origen->nombre }}</td>
                            <td class="px-4 py-2 border">{{ $traslado->destino->nombre }}</td>
                            <td class="px-4 py-2 border">{{ $traslado->observaciones ?? '-' }}</td>
                            <td class="px-4 py-2 text-center border">
                                <a href="{{ route('traslados.show', $traslado) }}"
                                    class="text-blue-600 hover:underline">
                                        Ver detalle
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">No hay traslados registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $traslados->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
