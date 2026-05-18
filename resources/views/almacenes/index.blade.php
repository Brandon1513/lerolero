<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Almacenes</h2>
                <p class="text-sm text-gray-500 mt-0.5">Gestión de almacenes generales y de vendedores</p>
            </div>
            @can('almacenes.crear')
            <a href="{{ route('almacenes.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Almacén
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="max-w-6xl py-8 mx-auto sm:px-6 lg:px-8">

        {{-- Filtros --}}
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('almacenes.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Buscar</label>
                    <div class="relative">
                        <svg class="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre del almacén..."
                            class="w-full py-2 pr-3 text-sm transition border border-gray-300 rounded-lg outline-none pl-9 focus:ring-2 focus:ring-indigo-500"/>
                    </div>
                </div>
                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Tipo</label>
                    <select name="tipo" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="general"  {{ request('tipo') === 'general'  ? 'selected' : '' }}>General</option>
                        <option value="vendedor" {{ request('tipo') === 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                        <option value="rechazo"  {{ request('tipo') === 'rechazo'  ? 'selected' : '' }}>Rechazo</option>
                    </select>
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Estado</label>
                    <select name="estado" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Filtrar
                    </button>
                    @if(request()->hasAny(['buscar','tipo','estado']))
                        <a href="{{ route('almacenes.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-indigo-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $statsTotal }}</div><div class="text-xs text-gray-500">Total</div></div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-green-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $statsGenerales }}</div><div class="text-xs text-gray-500">Generales</div></div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-blue-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $statsVendedores }}</div><div class="text-xs text-gray-500">Vendedores</div></div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-gray-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $statsInactivos }}</div><div class="text-xs text-gray-500">Inactivos</div></div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">
                    Mostrando <span class="font-semibold text-gray-900">{{ $almacenes->firstItem() ?? 0 }}</span>
                    – <span class="font-semibold text-gray-900">{{ $almacenes->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-900">{{ $almacenes->total() }}</span> almacenes
                </span>
                <span class="text-xs text-gray-400">Página {{ $almacenes->currentPage() }} de {{ $almacenes->lastPage() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Almacén</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Tipo</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Ubicación</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Usuario</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Estado</th>
                            @can('almacenes.editar')
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($almacenes as $almacen)
                            @php $tipo = $almacen->tipo ?? 'general'; @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ !$almacen->activo ? 'opacity-60' : '' }}">

                                {{-- Nombre --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 font-bold text-xs
                                            {{ $tipo === 'vendedor' ? 'bg-blue-100 text-blue-700' : ($tipo === 'rechazo' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700') }}">
                                            {{ strtoupper(substr($almacen->nombre, 0, 2)) }}
                                        </div>
                                        <span class="font-semibold text-gray-900">{{ $almacen->nombre }}</span>
                                    </div>
                                </td>

                                {{-- Tipo --}}
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full
                                        {{ $tipo === 'vendedor' ? 'bg-blue-100 text-blue-800' : ($tipo === 'rechazo' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-800') }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $tipo === 'vendedor' ? 'bg-blue-500' : ($tipo === 'rechazo' ? 'bg-red-400' : 'bg-green-500') }}"></span>
                                        {{ ucfirst($tipo) }}
                                    </span>
                                </td>

                                {{-- Ubicación --}}
                                <td class="px-5 py-3.5 text-gray-600 text-sm">{{ $almacen->ubicacion ?? '—' }}</td>

                                {{-- Usuario --}}
                                <td class="px-5 py-3.5">
                                    @if($almacen->usuario)
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center justify-center w-6 h-6 text-xs font-semibold text-indigo-700 bg-indigo-100 rounded-full shrink-0">
                                                {{ strtoupper(substr($almacen->usuario->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $almacen->usuario->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-5 py-3.5 text-center">
                                    @if($almacen->activo)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                @can('almacenes.editar')
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('almacenes.edit', $almacen) }}" title="Editar"
                                            class="inline-flex items-center justify-center w-8 h-8 transition-colors border rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border-amber-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('almacenes.toggle', $almacen) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $almacen->activo ? 'Inactivar' : 'Activar' }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors border
                                                    {{ $almacen->activo ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100 border-yellow-200' : 'bg-green-50 text-green-600 hover:bg-green-100 border-green-200' }}">
                                                @if($almacen->activo)
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                                        <div class="font-medium text-gray-500">Sin almacenes</div>
                                        @if(request()->hasAny(['buscar','tipo','estado']))
                                            <a href="{{ route('almacenes.index') }}" class="text-sm text-indigo-600 hover:underline">Limpiar filtros</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($almacenes->hasPages())
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="text-sm text-gray-600">{{ $almacenes->total() }} resultados</div>
                    <div class="flex items-center gap-1">
                        @if($almacenes->onFirstPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                        @else
                            <a href="{{ $almacenes->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                        @endif
                        @foreach($almacenes->getUrlRange(max(1,$almacenes->currentPage()-2), min($almacenes->lastPage(),$almacenes->currentPage()+2)) as $page => $url)
                            @if($page == $almacenes->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-white bg-indigo-600 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}" class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($almacenes->hasMorePages())
                            <a href="{{ $almacenes->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>