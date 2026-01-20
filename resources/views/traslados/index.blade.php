<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Historial de Traslados</h2>
    </x-slot>

    <div class="max-w-6xl py-12 mx-auto sm:px-6 lg:px-8">
        <!-- Botón Crear -->
        <div class="mb-6">
            <a href="{{ route('traslados.create') }}"
               class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                Crear Traslado
            </a>
        </div>

        <!-- Filtros -->
        <form method="GET" class="flex flex-wrap items-end gap-4 mb-6">
            <div>
                <x-input-label for="fecha_inicio" value="Fecha Inicio" />
                <x-text-input type="date" name="fecha_inicio" id="fecha_inicio" class="block w-full mt-1"
                    value="{{ request('fecha_inicio') }}" />
            </div>

            <div>
                <x-input-label for="fecha_fin" value="Fecha Fin" />
                <x-text-input type="date" name="fecha_fin" id="fecha_fin" class="block w-full mt-1"
                    value="{{ request('fecha_fin') }}" />
            </div>

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

            <div class="flex items-end gap-2">
                <x-primary-button class="h-[42px]">Filtrar</x-primary-button>

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
                                <div class="flex flex-wrap justify-center gap-2">
                                    <a href="{{ route('traslados.show', $traslado) }}"
                                       class="px-3 py-1 text-blue-600 hover:underline">
                                        Ver detalle
                                    </a>

                                    {{-- Eliminar: solo si se puede --}}
                                    @if(($traslado->puede_eliminar ?? false) === true)
                                        <form action="{{ route('traslados.destroy', $traslado) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Seguro que deseas eliminar este traslado? Se regresará el inventario al almacén origen.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-700">
                                                Eliminar
                                            </button>
                                        </form>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold text-white bg-gray-400 rounded">
                                            No eliminable
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-500">
                                No hay traslados registrados.
                            </td>
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
