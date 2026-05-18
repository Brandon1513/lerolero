<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Clientes</h2>
                <p class="text-sm text-gray-500 mt-0.5">Directorio de clientes y sus asignaciones</p>
            </div>
            @can('clientes.crear')
            <a href="{{ route('clientes.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar Cliente
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Filtros --}}
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('clientes.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Buscar</label>
                    <div class="relative">
                        <svg class="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
                        <input type="text" name="nombre" value="{{ request('nombre') }}" placeholder="Nombre del cliente..."
                            class="w-full py-2 pr-3 text-sm transition border border-gray-300 rounded-lg outline-none pl-9 focus:ring-2 focus:ring-indigo-500"/>
                    </div>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Vendedor</label>
                    <select name="asignado_a" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}" {{ request('asignado_a') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                        @endforeach
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
                    @if(request()->hasAny(['nombre','asignado_a','estado']))
                        <a href="{{ route('clientes.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Stats --}}
        @php
            $totalClientes   = $clientes->total();
            $activosCount    = $clientes->getCollection()->where('activo', true)->count();
            $inactivosCount  = $clientes->getCollection()->where('activo', false)->count();
            $sinAsignarCount = $clientes->getCollection()->whereNull('asignado_a')->count();
        @endphp
        <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-indigo-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $totalClientes }}</div><div class="text-xs text-gray-500">Total</div></div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-green-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $activosCount }}</div><div class="text-xs text-gray-500">Activos (pág.)</div></div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-gray-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $inactivosCount }}</div><div class="text-xs text-gray-500">Inactivos (pág.)</div></div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center rounded-lg w-9 h-9 bg-amber-100 shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $sinAsignarCount }}</div><div class="text-xs text-gray-500">Sin asignar (pág.)</div></div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">
                    Mostrando <span class="font-semibold text-gray-900">{{ $clientes->firstItem() ?? 0 }}</span>
                    – <span class="font-semibold text-gray-900">{{ $clientes->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-900">{{ $clientes->total() }}</span> clientes
                </span>
                <span class="text-xs text-gray-400">Página {{ $clientes->currentPage() }} de {{ $clientes->lastPage() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Cliente</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Dirección</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Teléfono</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Asignado a</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Nivel</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Días visita</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Estado</th>
                            @canany(['clientes.editar','clientes.eliminar'])
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($clientes as $cliente)
                            @php $puedeEliminar = $cliente->puede_eliminar ?? true; @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ !$cliente->activo ? 'opacity-60' : '' }}">

                                {{-- Cliente --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex items-center justify-center text-sm font-bold text-indigo-700 bg-indigo-100 rounded-full w-9 h-9 shrink-0">
                                            {{ strtoupper(substr($cliente->nombre, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $cliente->nombre }}</div>
                                            @if($cliente->tiene_historial ?? false)
                                                <div class="text-xs text-blue-600 flex items-center gap-1 mt-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                                    Con historial
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Dirección --}}
                                <td class="px-5 py-3.5">
                                    @if($cliente->calle || $cliente->colonia)
                                        <div class="text-sm text-gray-700">{{ $cliente->calle }}</div>
                                        <div class="text-xs text-gray-400">{{ $cliente->colonia }}{{ $cliente->codigo_postal ? ', CP '.$cliente->codigo_postal : '' }}</div>
                                    @else
                                        <span class="text-xs text-gray-400">No especificada</span>
                                    @endif
                                </td>

                                {{-- Teléfono --}}
                                <td class="px-5 py-3.5 text-sm text-gray-600">{{ $cliente->telefono ?: '—' }}</td>

                                {{-- Asignado a --}}
                                <td class="px-5 py-3.5">
                                    @if($cliente->asignadoA)
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center justify-center w-6 h-6 text-xs font-bold text-blue-700 bg-blue-100 rounded-full shrink-0">
                                                {{ strtoupper(substr($cliente->asignadoA->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $cliente->asignadoA->name }}</span>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">Sin asignar</span>
                                    @endif
                                </td>

                                {{-- Nivel --}}
                                <td class="px-5 py-3.5">
                                    @if($cliente->nivelPrecio)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">{{ $cliente->nivelPrecio->nombre }}</span>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Días de visita --}}
                                <td class="px-5 py-3.5">
                                    @php
                                        $dias = $cliente->dias_visita ?? [];
                                        if (is_string($dias)) $dias = explode(',', $dias);
                                        $abrev = ['Lunes'=>'L','Martes'=>'Ma','Miércoles'=>'Mi','Jueves'=>'J','Viernes'=>'V','Sábado'=>'S','Domingo'=>'D'];
                                    @endphp
                                    @if(count($dias) > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($dias as $dia)
                                                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-indigo-700 bg-indigo-100 rounded-full" title="{{ $dia }}">
                                                    {{ $abrev[trim($dia)] ?? substr(trim($dia),0,1) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Estado --}}
                                <td class="px-5 py-3.5 text-center">
                                    @if($cliente->activo)
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
                                @canany(['clientes.editar','clientes.eliminar'])
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">

                                        @can('clientes.editar')
                                        {{-- Editar --}}
                                        <a href="{{ route('clientes.edit', $cliente) }}" title="Editar"
                                            class="inline-flex items-center justify-center w-8 h-8 transition-colors border rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border-amber-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        {{-- Toggle --}}
                                        <form action="{{ route('clientes.toggle', $cliente) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $cliente->activo ? 'Inactivar' : 'Activar' }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors border
                                                    {{ $cliente->activo ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100 border-yellow-200' : 'bg-green-50 text-green-600 hover:bg-green-100 border-green-200' }}">
                                                @if($cliente->activo)
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                        @endcan

                                        @can('clientes.eliminar')
                                        {{-- Eliminar --}}
                                        @if($puedeEliminar)
                                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar al cliente {{ addslashes($cliente->nombre) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Eliminar"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 transition-colors border border-red-200 rounded-lg bg-red-50 hover:bg-red-100">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" title="Tiene historial — no eliminable"
                                                class="inline-flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                        @endcan

                                    </div>
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                        <div class="font-medium text-gray-500">Sin resultados</div>
                                        @if(request()->hasAny(['nombre','asignado_a','estado']))
                                            <a href="{{ route('clientes.index') }}" class="text-sm text-indigo-600 hover:underline">Limpiar filtros</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($clientes->hasPages())
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="text-sm text-gray-600">{{ $clientes->total() }} clientes en total</div>
                    <div class="flex items-center gap-1">
                        @if($clientes->onFirstPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                        @else
                            <a href="{{ $clientes->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                        @endif
                        @foreach($clientes->getUrlRange(max(1,$clientes->currentPage()-2), min($clientes->lastPage(),$clientes->currentPage()+2)) as $page => $url)
                            @if($page == $clientes->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-white bg-indigo-600 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}" class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($clientes->hasMorePages())
                            <a href="{{ $clientes->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>