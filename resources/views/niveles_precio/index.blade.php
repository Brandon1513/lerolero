<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Lista de Niveles de Precio') }}
        </h2>
    </x-slot>

    <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">

                <div class="mb-4">
                    <a href="{{ route('niveles-precio.create') }}"
                       class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                        Agregar Nivel de Precio
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border border-collapse border-gray-200 divide-y divide-gray-200">
                        <thead class="text-left bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">Nombre</th>
                                <th class="px-4 py-2 border">Estado</th>
                                <th class="px-4 py-2 text-center border">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($niveles as $nivel)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">
                                        {{ $nivel->nombre }}
                                    </td>

                                    <td class="px-4 py-2 border">
                                        <span class="px-2 py-1 text-xs font-semibold rounded
                                            {{ $nivel->activo ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                                            {{ $nivel->activo ? 'Activo' : 'Inactivo' }}
                                        </span>

                                        @if(($nivel->tiene_uso ?? false) === true)
                                            <span class="ml-2 text-xs text-gray-500">
                                                (en uso)
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-2 text-center border">
                                        <div class="flex flex-wrap justify-center gap-2">
                                            {{-- Editar (SIEMPRE) --}}
                                            <a href="{{ route('niveles-precio.edit', $nivel) }}"
                                               class="px-3 py-1 text-white bg-yellow-500 rounded hover:bg-yellow-700">
                                                Editar
                                            </a>

                                            {{-- Eliminar (solo si puede) --}}
                                            @if(($nivel->puede_eliminar ?? true) === true)
                                                <form action="{{ route('niveles-precio.destroy', $nivel) }}" method="POST"
                                                      onsubmit="return confirm('¿Eliminar este nivel de precio?')">
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

                                            {{-- Activar / Inactivar --}}
                                            <form action="{{ route('niveles-precio.toggle', $nivel) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1 rounded text-white
                                                    {{ $nivel->activo ? 'bg-gray-500 hover:bg-gray-700' : 'bg-green-600 hover:bg-green-700' }}">
                                                    {{ $nivel->activo ? 'Inactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-center text-gray-500">
                                        No hay niveles de precio registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
