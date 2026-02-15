<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Historial de Traslados</h2>
                <p class="text-sm text-gray-500 mt-0.5">Movimientos de mercancía entre almacenes</p>
            </div>
            <a href="{{ route('traslados.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Traslado
            </a>
        </div>
    </x-slot>

    <div class="max-w-6xl py-8 mx-auto sm:px-6 lg:px-8">
        {{-- ── FILTROS ── --}}
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('traslados.index') }}" class="flex flex-wrap items-end gap-3">

                {{-- Fecha inicio --}}
                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                        class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
                </div>

                {{-- Fecha fin --}}
                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                        class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
                </div>

                {{-- Origen --}}
                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Origen</label>
                    <select name="origen_id"
                        class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('origen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Destino --}}
                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Destino</label>
                    <select name="destino_id"
                        class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('destino_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Botones --}}
                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filtrar
                    </button>
                    @if(request()->hasAny(['fecha_inicio','fecha_fin','origen_id','destino_id']))
                        <a href="{{ route('traslados.index') }}"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsTotal }}</div>
                    <div class="text-xs text-gray-500">Total traslados</div>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsSemana }}</div>
                    <div class="text-xs text-gray-500">Esta semana</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-purple-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsProductosMovidos }}</div>
                    <div class="text-xs text-gray-500">Productos movidos hoy</div>
                </div>
            </div>
        </div>

        {{-- ── TABLA ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">

            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">
                    Mostrando <span class="font-semibold text-gray-900">{{ $traslados->firstItem() ?? 0 }}</span>
                    – <span class="font-semibold text-gray-900">{{ $traslados->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-900">{{ $traslados->total() }}</span> traslados
                </span>
                <span class="text-xs text-gray-400">Página {{ $traslados->currentPage() }} de {{ $traslados->lastPage() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Fecha</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Origen</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Destino</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Observaciones</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($traslados as $traslado)
                            <tr class="transition-colors hover:bg-gray-50">

                                {{-- Fecha --}}
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($traslado->fecha)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($traslado->fecha)->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Origen --}}
                                <td class="px-5 py-3.5">
                                    @php $tipoOrigen = $traslado->origen->tipo ?? 'general'; @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full
                                        {{ $tipoOrigen === 'vendedor'  ? 'bg-blue-100 text-blue-800'   :
                                          ($tipoOrigen === 'rechazo'   ? 'bg-red-100 text-red-700'     :
                                          ($tipoOrigen === 'general'   ? 'bg-green-100 text-green-800' :
                                                                         'bg-gray-100 text-gray-700')) }}">
                                        {{ $traslado->origen->nombre }}
                                    </span>
                                </td>

                                {{-- Destino --}}
                                <td class="px-5 py-3.5">
                                    @php $tipoDestino = $traslado->destino->tipo ?? 'general'; @endphp
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full
                                        {{ $tipoDestino === 'vendedor'  ? 'bg-blue-100 text-blue-800'   :
                                          ($tipoDestino === 'rechazo'   ? 'bg-red-100 text-red-700'     :
                                          ($tipoDestino === 'general'   ? 'bg-green-100 text-green-800' :
                                                                          'bg-gray-100 text-gray-700')) }}">
                                        {{ $traslado->destino->nombre }}
                                    </span>
                                </td>

                                {{-- Observaciones --}}
                                <td class="px-5 py-3.5 text-gray-500 text-sm max-w-xs truncate">
                                    {{ $traslado->observaciones ?? '—' }}
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">

                                        {{-- Ver detalle --}}
                                        <a href="{{ route('traslados.show', $traslado) }}"
                                            title="Ver detalle"
                                            class="inline-flex items-center justify-center w-8 h-8 text-blue-600 transition-colors border border-blue-200 rounded-lg bg-blue-50 hover:bg-blue-100">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        {{-- Eliminar --}}
                                        @if(($traslado->puede_eliminar ?? false) === true)
                                            <form action="{{ route('traslados.destroy', $traslado) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar este traslado? Se revertirá el inventario al almacén origen.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Eliminar"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 transition-colors border border-red-200 rounded-lg bg-red-50 hover:bg-red-100">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" title="No se puede eliminar: hay ventas o movimientos posteriores"
                                                class="inline-flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <div class="font-medium text-gray-500">Sin traslados</div>
                                        <div class="text-sm text-gray-400">No se encontraron traslados con los filtros aplicados.</div>
                                        @if(request()->hasAny(['fecha_inicio','fecha_fin','origen_id','destino_id']))
                                            <a href="{{ route('traslados.index') }}" class="text-sm text-indigo-600 hover:underline">Limpiar filtros</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── PAGINADO ── --}}
            @if($traslados->hasPages())
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="text-sm text-gray-600">{{ $traslados->total() }} resultados</div>
                    <div class="flex items-center gap-1">
                        @if($traslados->onFirstPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        @else
                            <a href="{{ $traslados->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        @endif

                        @foreach($traslados->getUrlRange(max(1,$traslados->currentPage()-2), min($traslados->lastPage(),$traslados->currentPage()+2)) as $page => $url)
                            @if($page == $traslados->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-white bg-indigo-600 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        @if($traslados->hasMorePages())
                            <a href="{{ $traslados->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
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