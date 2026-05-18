<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Niveles de Precio</h2>
                <p class="text-sm text-gray-500 mt-0.5">Categorías de precio asignadas a clientes y productos</p>
            </div>
            @can('niveles_precio.crear')
            <a href="{{ route('niveles-precio.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar Nivel
            </a>
            @endcan
        </div>
    </x-slot>
    <div class="max-w-3xl py-8 mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-3 mb-4">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl"><div><div class="text-xl font-bold text-gray-900">{{ $niveles->count() }}</div><div class="text-xs text-gray-500">Total</div></div></div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl"><div><div class="text-xl font-bold text-gray-900">{{ $niveles->where('activo', true)->count() }}</div><div class="text-xs text-gray-500">Activos</div></div></div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl"><div><div class="text-xl font-bold text-gray-900">{{ $niveles->where('tiene_uso', true)->count() }}</div><div class="text-xs text-gray-500">En uso</div></div></div>
        </div>
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600"><span class="font-semibold text-gray-900">{{ $niveles->count() }}</span> niveles registrados</span>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Nombre</th>
                        <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Estado</th>
                        @canany(['niveles_precio.editar','niveles_precio.eliminar'])
                        <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($niveles as $nivel)
                        <tr class="hover:bg-gray-50 transition-colors {{ !$nivel->activo ? 'opacity-60' : '' }}">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-indigo-700 bg-indigo-100 rounded-lg shrink-0">{{ strtoupper(substr($nivel->nombre, 0, 2)) }}</div>
                                    <div>
                                        <div class="font-semibold text-gray-900">{{ $nivel->nombre }}</div>
                                        @if($nivel->tiene_uso)<div class="text-xs text-amber-600 mt-0.5">En uso por clientes o productos</div>@endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($nivel->activo)<span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo</span>
                                @else<span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo</span>@endif
                            </td>
                            @canany(['niveles_precio.editar','niveles_precio.eliminar'])
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    @can('niveles_precio.editar')
                                    <a href="{{ route('niveles-precio.edit', $nivel) }}" title="Editar" class="inline-flex items-center justify-center w-8 h-8 transition-colors border rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border-amber-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('niveles-precio.toggle', $nivel) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="{{ $nivel->activo ? 'Inactivar' : 'Activar' }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors border {{ $nivel->activo ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100 border-yellow-200' : 'bg-green-50 text-green-600 hover:bg-green-100 border-green-200' }}">
                                            @if($nivel->activo)<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                                            @else<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>@endif
                                        </button>
                                    </form>
                                    @endcan
                                    @can('niveles_precio.eliminar')
                                    @if($nivel->puede_eliminar ?? true)
                                        <form action="{{ route('niveles-precio.destroy', $nivel) }}" method="POST" onsubmit="return confirm('¿Eliminar el nivel {{ addslashes($nivel->nombre) }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" title="Eliminar" class="inline-flex items-center justify-center w-8 h-8 text-red-600 transition-colors border border-red-200 rounded-lg bg-red-50 hover:bg-red-100">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" title="No se puede eliminar: está en uso" class="inline-flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-16 text-center"><div class="font-medium text-gray-500">No hay niveles de precio</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>