{{-- resources/views/vendedores/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Lista de Vendedores') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Filtros --}}
            <div class="p-6 mb-4 bg-white rounded shadow-sm">
                <form method="GET" action="{{ route('vendedores.index') }}" class="flex flex-wrap items-end gap-4">

                    {{-- Nombre --}}
                    <div>
                        <x-input-label for="nombre" value="Nombre" />
                        <x-text-input
                            id="nombre"
                            name="nombre"
                            type="text"
                            placeholder="Buscar por nombre..."
                            :value="request('nombre')"
                        />
                    </div>

                    {{-- Estado --}}
                    <div>
                        <x-input-label for="estado" value="Estado" />
                        <select name="estado" id="estado" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Todos --</option>
                            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    {{-- Rol --}}
                    <div>
                        <x-input-label for="rol" value="Rol" />
                        <select name="rol" id="rol" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                            <option value="">-- Todos --</option>
                            <option value="vendedor" {{ request('rol') === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                            <option value="administrador" {{ request('rol') === 'administrador' ? 'selected' : '' }}>Administrador</option>
                        </select>
                    </div>

                    {{-- Botones --}}
                    <div class="flex gap-2">
                        <x-primary-button>FILTRAR</x-primary-button>

                        <a href="{{ route('vendedores.index') }}"
                           class="px-4 py-2 text-gray-800 bg-gray-300 rounded-md hover:bg-gray-400">
                            Limpiar
                        </a>
                    </div>
                </form>
            </div>

            {{-- Tabla --}}
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <a href="{{ route('vendedores.create') }}"
                       class="inline-block px-4 py-2 mb-4 text-white bg-blue-500 rounded-md hover:bg-blue-700">
                        Agregar Vendedor
                    </a>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border border-collapse border-gray-200">
                            <thead>
                                <tr class="text-left bg-gray-100">
                                    <th class="px-4 py-2 border">Nombre</th>
                                    <th class="px-4 py-2 border">Correo</th>
                                    <th class="px-4 py-2 border">Fecha de Registro</th>
                                    <th class="px-4 py-2 border">Rol</th>
                                    <th class="px-4 py-2 border">Estado</th>
                                    <th class="px-4 py-2 text-center border">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($vendedores as $vendedor)
                                    @php
                                        $esYo = auth()->id() === $vendedor->id;
                                        $esAdmin = $vendedor->hasRole('administrador');
                                        $query = request()->except('page'); // conservar filtros al accionar
                                    @endphp

                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $vendedor->name }}</td>
                                        <td class="px-4 py-2 border">{{ $vendedor->email }}</td>
                                        <td class="px-4 py-2 border">
                                            {{ optional($vendedor->created_at)->format('d/m/Y') }}
                                        </td>

                                        <td class="px-4 py-2 border">
                                            {{ $vendedor->getRoleNames()->implode(', ') }}
                                        </td>

                                        <td class="px-4 py-2 border">
                                            @if($vendedor->activo)
                                                <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded">
                                                    Activo
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold text-gray-700 bg-gray-100 rounded">
                                                    Inactivo
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-2 text-center border">
                                            <div class="flex flex-wrap justify-center gap-2">

                                                {{-- Editar: permitido --}}
                                                <a href="{{ route('vendedores.edit', $vendedor) }}"
                                                   class="px-3 py-1 text-white bg-yellow-500 rounded-md hover:bg-yellow-700">
                                                    Editar
                                                </a>

                                                {{-- Eliminar:
                                                     - NO si es tu usuario
                                                     - NO si es administrador
                                                     - NO si tiene movimientos (puede_eliminar false)
                                                --}}
                                                @if(!$esYo && !$esAdmin && ($vendedor->puede_eliminar ?? true))
                                                    <form action="{{ route('vendedores.destroy', $vendedor) }}" method="POST"
                                                          onsubmit="return confirm('¿Eliminar vendedor?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="px-3 py-1 text-white bg-red-500 rounded-md hover:bg-red-700">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="px-3 py-1 text-xs font-semibold text-white bg-gray-400 rounded-md">
                                                        No eliminable
                                                    </span>
                                                @endif

                                                {{-- Activar/Inactivar:
                                                     - NO permitir que el usuario se inactive a sí mismo
                                                     (la validación fuerte debe estar en el controller, esto es solo UI)
                                                --}}
                                                @if(!$esYo)
                                                    <form action="{{ route('vendedores.toggle', $vendedor) }}?{{ http_build_query($query) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="px-3 py-1 text-white rounded-md
                                                                {{ $vendedor->activo ? 'bg-gray-500 hover:bg-gray-700' : 'bg-green-500 hover:bg-green-700' }}">
                                                            {{ $vendedor->activo ? 'Inactivar' : 'Activar' }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="px-3 py-1 text-xs font-semibold text-white bg-gray-400 rounded-md">
                                                        Tu usuario
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                            No hay vendedores que coincidan con los filtros.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
