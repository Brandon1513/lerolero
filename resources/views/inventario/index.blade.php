<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900">Inventario General</h2>
            <p class="text-sm text-gray-500 mt-0.5">Stock por almacén, producto y lote</p>
        </div>
    </x-slot>

    <div class="py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- ── FILTROS ── --}}
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('inventario.index') }}" class="flex flex-wrap items-end gap-3">

                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Producto</label>
                    <div class="relative">
                        <svg class="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                        </svg>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar producto..."
                            class="w-full py-2 pr-3 text-sm transition border border-gray-300 rounded-lg outline-none pl-9 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
                    </div>
                </div>

                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Almacén</label>
                    <select name="almacen_id" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        @foreach($almacenes as $alm)
                            <option value="{{ $alm->id }}" {{ request('almacen_id') == $alm->id ? 'selected' : '' }}>
                                {{ $alm->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Caducidad</label>
                    <select name="caducidad" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todas</option>
                        <option value="vencido"  {{ request('caducidad') === 'vencido'  ? 'selected' : '' }}>Vencidos</option>
                        <option value="pronto"   {{ request('caducidad') === 'pronto'   ? 'selected' : '' }}>Próx. 30 días</option>
                        <option value="vigente"  {{ request('caducidad') === 'vigente'  ? 'selected' : '' }}>Vigentes</option>
                    </select>
                </div>

                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Stock</label>
                    <select name="stock" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="con_stock" {{ request('stock', 'con_stock') === 'con_stock' ? 'selected' : '' }}>Con stock</option>
                        <option value="sin_stock" {{ request('stock') === 'sin_stock' ? 'selected' : '' }}>Sin stock (0)</option>
                        <option value="todos"     {{ request('stock') === 'todos'     ? 'selected' : '' }}>Todos</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filtrar
                    </button>
                    @if(request()->hasAny(['buscar','almacen_id','caducidad']) || request('stock', 'con_stock') !== 'con_stock')
                        <a href="{{ route('inventario.index') }}"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ number_format($statsTotalUnidades) }}</div>
                    <div class="text-xs text-gray-500">Unidades totales</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-green-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsProductosDistintos }}</div>
                    <div class="text-xs text-gray-500">Productos distintos</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center rounded-lg w-9 h-9 bg-amber-100 shrink-0">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsProximosVencer }}</div>
                    <div class="text-xs text-gray-500">Vencen en 30 días</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-red-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsVencidos }}</div>
                    <div class="text-xs text-gray-500">Lotes vencidos</div>
                </div>
            </div>
        </div>

        {{-- ── TABLA ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">

            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">
                    Mostrando <span class="font-semibold text-gray-900">{{ $inventarios->firstItem() ?? 0 }}</span>
                    – <span class="font-semibold text-gray-900">{{ $inventarios->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-900">{{ $inventarios->total() }}</span> lotes
                </span>
                <span class="text-xs text-gray-400">Página {{ $inventarios->currentPage() }} de {{ $inventarios->lastPage() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Producto</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Lote</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Caduca</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Cantidad</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Almacén</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($inventarios as $inv)
                            @php
                                $caducidad     = $inv->fecha_caducidad ? \Carbon\Carbon::parse($inv->fecha_caducidad) : null;
                                $vencido       = $caducidad && $caducidad->isPast();
                                $proximoVencer = $caducidad && !$vencido && $caducidad->diffInDays(now()) <= 30;
                                $tipo          = $inv->almacen->tipo ?? 'general';
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ $vencido ? 'bg-red-50/40' : '' }}">

                                {{-- Producto --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex items-center justify-center text-xs font-bold text-indigo-700 bg-indigo-100 rounded-md w-7 h-7 shrink-0">
                                            {{ strtoupper(substr($inv->producto->nombre ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $inv->producto->nombre }}</div>
                                            @if($inv->producto->categoria)
                                                <div class="text-xs text-gray-400">{{ $inv->producto->categoria->nombre }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Lote --}}
                                <td class="px-5 py-3.5">
                                    @if($inv->lote)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold rounded bg-gray-100 text-gray-700">
                                            {{ $inv->lote }}
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
                                        @if($vencido)
                                            <div class="text-xs text-red-500 mt-0.5">Hace {{ abs($caducidad->diffInDays(now())) }}d</div>
                                        @elseif($proximoVencer)
                                            <div class="text-xs text-amber-600 mt-0.5">En {{ $caducidad->diffInDays(now()) }}d</div>
                                        @endif
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                {{-- Cantidad --}}
                                <td class="px-5 py-3.5 text-right">
                                    @if($inv->cantidad == 0)
                                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">
                                            0 uds
                                        </span>
                                    @else
                                        <span class="font-bold {{ $vencido ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ number_format($inv->cantidad) }}
                                        </span>
                                        <span class="text-xs text-gray-400 ml-0.5">uds</span>
                                    @endif
                                </td>

                                {{-- Almacén --}}
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full
                                        {{ $tipo === 'vendedor' ? 'bg-blue-100 text-blue-800' : ($tipo === 'rechazo' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-800') }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $tipo === 'vendedor' ? 'bg-blue-500' : ($tipo === 'rechazo' ? 'bg-red-400' : 'bg-green-500') }}"></span>
                                        {{ $inv->almacen->nombre }}
                                    </span>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                        </svg>
                                        <div class="font-medium text-gray-500">Sin resultados</div>
                                        <div class="text-sm text-gray-400">No hay lotes con los filtros aplicados.</div>
                                        @if(request()->hasAny(['buscar','almacen_id','caducidad']) || request('stock') !== 'todos')
                                            <a href="{{ route('inventario.index') }}" class="text-sm text-indigo-600 hover:underline">Limpiar filtros</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── PAGINADO ── --}}
            @if($inventarios->hasPages())
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="text-sm text-gray-600">{{ $inventarios->total() }} lotes</div>
                    <div class="flex items-center gap-1">
                        @if($inventarios->onFirstPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </span>
                        @else
                            <a href="{{ $inventarios->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            </a>
                        @endif
                        @foreach($inventarios->getUrlRange(max(1,$inventarios->currentPage()-2), min($inventarios->lastPage(),$inventarios->currentPage()+2)) as $page => $url)
                            @if($page == $inventarios->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-white bg-indigo-600 rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">{{ $page }}</a>
                            @endif
                        @endforeach
                        @if($inventarios->hasMorePages())
                            <a href="{{ $inventarios->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
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