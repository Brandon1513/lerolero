<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Historial de Producción</h2>
                <p class="text-sm text-gray-500 mt-0.5">Registro de entradas al almacén general</p>
            </div>
            <a href="{{ route('producciones.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Registrar Producción
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl py-8 mx-auto sm:px-6 lg:px-8">

        {{-- ── ALERTAS ── --}}
        @if(session('success'))
            <div class="flex items-center gap-3 px-4 py-3 mb-4 text-sm text-green-800 border border-green-200 bg-green-50 rounded-xl">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 px-4 py-3 mb-4 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm0-4a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- ── FILTROS ── --}}
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('producciones.index') }}" class="flex flex-wrap items-end gap-3">

                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                        class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
                </div>

                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                        class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
                </div>

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Producto</label>
                    <div class="relative">
                        <svg class="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                        </svg>
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                            placeholder="Nombre del producto..."
                            class="w-full py-2 pr-3 text-sm transition border border-gray-300 rounded-lg outline-none pl-9 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filtrar
                    </button>
                    @if(request()->hasAny(['fecha_inicio','fecha_fin','buscar']))
                        <a href="{{ route('producciones.index') }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── STATS ── --}}
        <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-indigo-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsTotal }}</div>
                    <div class="text-xs text-gray-500">Total registros</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-blue-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsHoy }}</div>
                    <div class="text-xs text-gray-500">Hoy</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-green-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ number_format($statsUnidadesHoy) }}</div>
                    <div class="text-xs text-gray-500">Unidades hoy</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-purple-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ number_format($statsUnidadesMes) }}</div>
                    <div class="text-xs text-gray-500">Unidades este mes</div>
                </div>
            </div>
        </div>

        {{-- ── TABLA ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">

            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">
                    Mostrando <span class="font-semibold text-gray-900">{{ $producciones->firstItem() ?? 0 }}</span>
                    – <span class="font-semibold text-gray-900">{{ $producciones->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-900">{{ $producciones->total() }}</span> registros
                </span>
                <span class="text-xs text-gray-400">Página {{ $producciones->currentPage() }} de {{ $producciones->lastPage() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Fecha</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Producto</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Cantidad</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Lote</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Caduca</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Registrado por</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Notas</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($producciones as $produccion)
                            @php
                                $caducidad = $produccion->fecha_caducidad
                                    ? \Carbon\Carbon::parse($produccion->fecha_caducidad)
                                    : null;
                                $vencido        = $caducidad && $caducidad->isPast();
                                $proximoVencer  = $caducidad && !$vencido && $caducidad->diffInDays(now()) <= 30;
                            @endphp
                            <tr class="transition-colors hover:bg-gray-50">

                                {{-- Fecha --}}
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($produccion->fecha)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($produccion->fecha)->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Producto --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex items-center justify-center text-xs font-bold text-indigo-700 bg-indigo-100 rounded-md w-7 h-7 shrink-0">
                                            {{ strtoupper(substr($produccion->producto->nombre ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $produccion->producto->nombre }}</span>
                                    </div>
                                </td>

                                {{-- Cantidad --}}
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-bold text-gray-900">{{ number_format($produccion->cantidad) }}</span>
                                    <span class="text-xs text-gray-400 ml-0.5">uds</span>
                                </td>

                                {{-- Lote --}}
                                <td class="px-5 py-3.5">
                                    @if($produccion->lote)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold rounded bg-gray-100 text-gray-700">
                                            {{ $produccion->lote }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Caducidad --}}
                                <td class="px-5 py-3.5">
                                    @if($caducidad)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full
                                            {{ $vencido ? 'bg-red-100 text-red-700' : ($proximoVencer ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $vencido ? 'bg-red-500' : ($proximoVencer ? 'bg-amber-500' : 'bg-green-500') }}"></span>
                                            {{ $caducidad->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Usuario --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center justify-center w-6 h-6 text-xs font-semibold text-gray-600 bg-gray-200 rounded-full shrink-0">
                                            {{ strtoupper(substr($produccion->usuario->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $produccion->usuario->name ?? '—' }}</span>
                                    </div>
                                </td>

                                {{-- Notas --}}
                                <td class="px-5 py-3.5 text-gray-500 text-sm max-w-[180px] truncate" title="{{ $produccion->notas }}">
                                    {{ $produccion->notas ?? '—' }}
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center">
                                        <form action="{{ route('producciones.destroy', $produccion) }}" method="POST"
                                            onsubmit="return confirm('¿Eliminar esta producción? Se revertirá el stock en el almacén general.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Eliminar"
                                                class="inline-flex items-center justify-center w-8 h-8 text-red-600 transition-colors border border-red-200 rounded-lg bg-red-50 hover:bg-red-100">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <div class="font-medium text-gray-500">Sin producciones</div>
                                        <div class="text-sm text-gray-400">No hay registros con los filtros aplicados.</div>
                                        @if(request()->hasAny(['fecha_inicio','fecha_fin','buscar']))
                                            <a href="{{ route('producciones.index') }}" class="text-sm text-indigo-600 hover:underline">Limpiar filtros</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── PAGINADO ── --}}
            @if($producciones->hasPages())
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="text-sm text-gray-600">{{ $producciones->total() }} resultados</div>
                    <div class="flex items-center gap-1">
                        @if($producciones->onFirstPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        @else
                            <a href="{{ $producciones->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        @endif
                        @foreach($producciones->getUrlRange(max(1,$producciones->currentPage()-2), min($producciones->lastPage(),$producciones->currentPage()+2)) as $page => $url)
                            @if($page == $producciones->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-white bg-indigo-600 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($producciones->hasMorePages())
                            <a href="{{ $producciones->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>