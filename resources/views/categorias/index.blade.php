<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Categorías</h2>
                <p class="text-sm text-gray-500 mt-0.5">Agrupa los productos por tipo o familia</p>
            </div>
            <a href="{{ route('categorias.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar Categoría
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl py-8 mx-auto sm:px-6 lg:px-8">

        {{-- Stats --}}
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-indigo-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $categorias->count() }}</div>
                    <div class="text-xs text-gray-500">Total</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-green-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $categorias->where('activo', true)->count() }}</div>
                    <div class="text-xs text-gray-500">Activas</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-purple-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $categorias->where('puede_eliminar', false)->count() }}</div>
                    <div class="text-xs text-gray-500">Con productos</div>
                </div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">

            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">
                    <span class="font-semibold text-gray-900">{{ $categorias->count() }}</span> categorías registradas
                </span>
            </div>

            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Nombre</th>
                        <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Estado</th>
                        <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categorias as $categoria)
                        <tr class="hover:bg-gray-50 transition-colors {{ !$categoria->activo ? 'opacity-60' : '' }}">

                            {{-- Nombre --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-purple-700 bg-purple-100 rounded-lg shrink-0">
                                        {{ strtoupper(substr($categoria->nombre, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $categoria->nombre }}</div>
                                        @if(!($categoria->puede_eliminar ?? true))
                                            <div class="text-xs text-purple-600 flex items-center gap-1 mt-0.5">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                                                Tiene productos asignados
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Estado --}}
                            <td class="px-5 py-3.5">
                                @if($categoria->activo)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activa
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactiva
                                    </span>
                                @endif
                            </td>

                            {{-- Acciones --}}
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">

                                    {{-- Editar --}}
                                    <a href="{{ route('categorias.edit', $categoria) }}" title="Editar"
                                        class="inline-flex items-center justify-center w-8 h-8 transition-colors border rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border-amber-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>

                                    {{-- Toggle --}}
                                    <form action="{{ route('categorias.toggle', $categoria) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="{{ $categoria->activo ? 'Inactivar' : 'Activar' }}"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors border
                                                {{ $categoria->activo
                                                    ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100 border-yellow-200'
                                                    : 'bg-green-50 text-green-600 hover:bg-green-100 border-green-200' }}">
                                            @if($categoria->activo)
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Eliminar --}}
                                    @if($categoria->puede_eliminar ?? true)
                                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar la categoría {{ addslashes($categoria->nombre) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Eliminar"
                                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 transition-colors border border-red-200 rounded-lg bg-red-50 hover:bg-red-100">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" title="No se puede eliminar: tiene productos asignados"
                                            class="inline-flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <div class="font-medium text-gray-500">No hay categorías registradas</div>
                                    <a href="{{ route('categorias.create') }}" class="text-sm text-indigo-600 hover:underline">Agregar la primera</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>