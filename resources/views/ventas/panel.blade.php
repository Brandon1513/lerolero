<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Panel de Ventas</h2>
            <p class="text-sm text-gray-500 mt-0.5">Resumen de ventas por vendedor y período</p>
        </div>
    </x-slot>

    <div class="py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- ── FILTROS ── --}}
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('ventas.panel') }}" class="flex flex-wrap items-end gap-3">

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Vendedor</label>
                    <select name="vendedor_id"
                        class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        @foreach($vendedores as $v)
                            <option value="{{ $v->id }}" {{ request('vendedor_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                        class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div class="min-w-[150px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                        class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Filtrar
                    </button>
                    @if(request()->hasAny(['vendedor_id','fecha_inicio','fecha_fin']))
                        <a href="{{ route('ventas.panel') }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
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
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $ventas->total() }}</div>
                    <div class="text-xs text-gray-500">Ventas</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-green-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">${{ number_format($totalGeneral, 2) }}</div>
                    <div class="text-xs text-gray-500">Total general</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-blue-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $vendedores->count() }}</div>
                    <div class="text-xs text-gray-500">Vendedores</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center rounded-lg w-9 h-9 bg-amber-100 shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">
                        ${{ $ventas->total() > 0 ? number_format($totalGeneral / $ventas->total(), 2) : '0.00' }}
                    </div>
                    <div class="text-xs text-gray-500">Promedio / venta</div>
                </div>
            </div>
        </div>

        {{-- ── TABLA ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">

            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">
                    Mostrando <span class="font-semibold text-gray-900">{{ $ventas->firstItem() ?? 0 }}</span>
                    – <span class="font-semibold text-gray-900">{{ $ventas->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-900">{{ $ventas->total() }}</span> ventas
                </span>
                <span class="text-xs text-gray-400">Página {{ $ventas->currentPage() }} de {{ $ventas->lastPage() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">#</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Fecha</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Cliente</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Vendedor</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Total</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($ventas as $venta)
                            <tr class="transition-colors hover:bg-gray-50">

                                {{-- ID --}}
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold rounded bg-gray-100 text-gray-600">
                                        #{{ $venta->id }}
                                    </span>
                                </td>

                                {{-- Fecha --}}
                                <td class="px-5 py-3.5">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($venta->fecha)->diffForHumans() }}
                                    </div>
                                </td>

                                {{-- Cliente --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center justify-center text-xs font-bold text-indigo-700 bg-indigo-100 rounded-full w-7 h-7 shrink-0">
                                            {{ strtoupper(substr($venta->cliente->nombre ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $venta->cliente->nombre ?? '—' }}</span>
                                    </div>
                                </td>

                                {{-- Vendedor --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="flex items-center justify-center text-xs font-bold text-blue-700 bg-blue-100 rounded-full w-7 h-7 shrink-0">
                                            {{ strtoupper(substr($venta->vendedor->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $venta->vendedor->name ?? '—' }}</span>
                                    </div>
                                </td>

                                {{-- Total --}}
                                <td class="px-5 py-3.5 text-right">
                                    <span class="font-bold text-gray-900">${{ number_format($venta->total, 2) }}</span>
                                </td>

                                {{-- Acción --}}
                                <td class="px-5 py-3.5 text-center">
                                    <a href="{{ route('ventas.show', $venta) }}" title="Ver detalle"
                                        class="inline-flex items-center justify-center w-8 h-8 text-indigo-600 transition-colors border border-indigo-200 rounded-lg bg-indigo-50 hover:bg-indigo-100">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <div class="font-medium text-gray-500">Sin ventas registradas</div>
                                        <div class="text-sm text-gray-400">Ajusta los filtros para ver resultados.</div>
                                        @if(request()->hasAny(['vendedor_id','fecha_inicio','fecha_fin']))
                                            <a href="{{ route('ventas.panel') }}" class="text-sm text-indigo-600 hover:underline">Limpiar filtros</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            @if($ventas->hasPages())
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="text-sm text-gray-600">{{ $ventas->total() }} resultados · ${{ number_format($totalGeneral, 2) }} total</div>
                    <div class="flex items-center gap-1">
                        @if($ventas->onFirstPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                        @else
                            <a href="{{ $ventas->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                        @endif
                        @foreach($ventas->getUrlRange(max(1,$ventas->currentPage()-2), min($ventas->lastPage(),$ventas->currentPage()+2)) as $page => $url)
                            @if($page == $ventas->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-white bg-indigo-600 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}" class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($ventas->hasMorePages())
                            <a href="{{ $ventas->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>