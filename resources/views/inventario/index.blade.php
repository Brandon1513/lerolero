<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Inventario General</h2>
                <p class="text-sm text-gray-500 mt-0.5">Stock por almacén, producto y lote</p>
            </div>
            <button onclick="abrirModalFirma()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-600 text-sm font-semibold rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar PDF
            </button>
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
    {{-- Modal de firma --}}
    <div id="modal-firma" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm mx-4">
            <h3 class="text-lg font-bold text-gray-900 mb-1">Firma de conformidad</h3>
            <p class="text-sm text-gray-500 mb-4">Firma para confirmar que revisaste el inventario. Se incluirá en el PDF.</p>

            <div class="mb-1 text-xs font-semibold text-gray-500 uppercase tracking-wide">Responsable</div>
            <div class="flex items-center gap-3 mb-4 px-3 py-2 bg-indigo-50 border border-indigo-100 rounded-lg">
                <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-indigo-700 bg-indigo-100 rounded-full shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                </div>
            </div>

            <div class="mb-1 text-xs font-semibold text-gray-500 uppercase tracking-wide">Firma</div>
            <canvas id="firma-inv-canvas" width="340" height="100"
                class="border-2 border-dashed border-gray-300 rounded-xl w-full cursor-crosshair bg-white touch-none mb-2"></canvas>

            <div class="flex gap-2 mb-4">
                <button type="button" onclick="limpiarFirmaInv()"
                    class="flex-1 py-2 text-sm font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Limpiar
                </button>
                <button type="button" onclick="cerrarModalFirma()"
                    class="flex-1 py-2 text-sm font-semibold text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    Cancelar
                </button>
            </div>

            <button type="button" onclick="generarPDFInventario()"
                class="w-full py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                Generar PDF con firma
            </button>
            <button type="button" onclick="generarPDFInventario(true)"
                class="w-full mt-2 py-2 text-xs font-semibold text-gray-500 hover:text-gray-700 transition">
                Generar sin firma
            </button>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
    // ── Modal de firma ────────────────────────────────────────────────
    const modalFirma = document.getElementById('modal-firma');
    const canvasInv  = document.getElementById('firma-inv-canvas');
    const ctxInv     = canvasInv?.getContext('2d');
    let drawingInv   = false;

    function abrirModalFirma() {
        modalFirma.classList.remove('hidden');
        modalFirma.classList.add('flex');
        limpiarFirmaInv();
    }

    function cerrarModalFirma() {
        modalFirma.classList.add('hidden');
        modalFirma.classList.remove('flex');
    }

    function limpiarFirmaInv() {
        ctxInv?.clearRect(0, 0, canvasInv.width, canvasInv.height);
    }

    if (canvasInv) {
        const getPos = (e) => {
            const r = canvasInv.getBoundingClientRect();
            const src = e.touches ? e.touches[0] : e;
            return { x: (src.clientX - r.left) * (canvasInv.width / r.width),
                     y: (src.clientY - r.top)  * (canvasInv.height / r.height) };
        };
        canvasInv.addEventListener('mousedown',  e => { drawingInv = true; const p = getPos(e); ctxInv.beginPath(); ctxInv.moveTo(p.x, p.y); });
        canvasInv.addEventListener('mousemove',  e => { if (!drawingInv) return; const p = getPos(e); ctxInv.lineWidth = 2.5; ctxInv.lineCap = 'round'; ctxInv.strokeStyle = '#1e1b4b'; ctxInv.lineTo(p.x, p.y); ctxInv.stroke(); });
        canvasInv.addEventListener('mouseup',    () => drawingInv = false);
        canvasInv.addEventListener('mouseleave', () => drawingInv = false);
        canvasInv.addEventListener('touchstart',  e => { e.preventDefault(); drawingInv = true; const p = getPos(e); ctxInv.beginPath(); ctxInv.moveTo(p.x, p.y); }, { passive: false });
        canvasInv.addEventListener('touchmove',   e => { e.preventDefault(); if (!drawingInv) return; const p = getPos(e); ctxInv.lineWidth = 2.5; ctxInv.lineCap = 'round'; ctxInv.strokeStyle = '#1e1b4b'; ctxInv.lineTo(p.x, p.y); ctxInv.stroke(); }, { passive: false });
        canvasInv.addEventListener('touchend',    () => drawingInv = false);
    }

    // Cerrar modal al hacer click fuera
    modalFirma?.addEventListener('click', e => { if (e.target === modalFirma) cerrarModalFirma(); });

    function generarPDFInventario(sinFirma = false) {
        cerrarModalFirma();
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });

        const azul  = [67, 56, 202];
        const gris  = [107, 114, 128];
        const negro = [17, 24, 39];
        const lineC = [229, 231, 235];

        // ── Encabezado ──
        doc.setFillColor(...azul);
        doc.rect(0, 0, 297, 14, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(11);
        doc.setFont('helvetica', 'bold');
        doc.text('Dulces Lero Lero — Inventario General', 14, 9.5);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.text('Generado: ' + new Date().toLocaleDateString('es-MX', { day:'2-digit', month:'2-digit', year:'numeric' }), 230, 9.5);

        // ── Filtros aplicados ──
        let y = 22;
        doc.setTextColor(...gris);
        doc.setFontSize(8);
        const filtros = [];
        @if(request('buscar'))       filtros.push('Producto: {{ request("buscar") }}'); @endif
        @if(request('almacen_id'))   filtros.push('Almacén: {{ $almacenes->firstWhere("id", request("almacen_id"))?->nombre ?? request("almacen_id") }}'); @endif
        @if(request('caducidad'))    filtros.push('Caducidad: {{ request("caducidad") }}'); @endif
        @if(request('stock','con_stock') !== 'con_stock') filtros.push('Stock: {{ request("stock") }}'); @endif
        if (filtros.length) doc.text('Filtros: ' + filtros.join(' · '), 14, y);

        // ── Stats rápidos ──
        y += 8;
        doc.setFillColor(248, 249, 250);
        doc.rect(14, y, 60, 12, 'F');
        doc.rect(80, y, 60, 12, 'F');
        doc.rect(146, y, 60, 12, 'F');
        doc.rect(212, y, 60, 12, 'F');
        doc.setTextColor(...azul);
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('{{ number_format($statsTotalUnidades) }}', 44, y+5, { align:'center' });
        doc.text('{{ $statsProductosDistintos }}', 110, y+5, { align:'center' });
        doc.text('{{ $statsProximosVencer }}', 176, y+5, { align:'center' });
        doc.text('{{ $statsVencidos }}', 242, y+5, { align:'center' });
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(7);
        doc.setTextColor(...gris);
        doc.text('Unidades totales', 44, y+10, { align:'center' });
        doc.text('Productos distintos', 110, y+10, { align:'center' });
        doc.text('Próx. a vencer (30d)', 176, y+10, { align:'center' });
        doc.text('Lotes vencidos', 242, y+10, { align:'center' });

        // ── Línea ──
        y += 18;
        doc.setDrawColor(...lineC);
        doc.line(14, y, 283, y);
        y += 5;

        // ── Cabecera tabla ──
        doc.setFillColor(243, 244, 246);
        doc.rect(14, y, 269, 7, 'F');
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(7.5);
        doc.setTextColor(...gris);
        doc.text('PRODUCTO',   16,  y+5);
        doc.text('CATEGORÍA',  90,  y+5);
        doc.text('LOTE',       135, y+5);
        doc.text('CADUCA',     170, y+5);
        doc.text('ESTADO',     205, y+5);
        doc.text('ALMACÉN',    235, y+5);
        doc.text('CANT.',      278, y+5, { align:'right' });
        y += 7;

        // ── Filas ──
        doc.setFont('helvetica', 'normal');
        @foreach($inventarios as $inv)
        @php
            $caducidad = $inv->fecha_caducidad ? \Carbon\Carbon::parse($inv->fecha_caducidad) : null;
            $vencido   = $caducidad && $caducidad->isPast();
            $pronto    = $caducidad && !$vencido && $caducidad->diffInDays(now()) <= 30;
            $estado    = $vencido ? 'Vencido' : ($pronto ? 'Próx. vencer' : 'Vigente');
            $estadoR   = $vencido ? [220,38,38] : ($pronto ? [180,83,9] : [4,120,87]);
        @endphp
        if (y > 185) { doc.addPage(); y = 14; }
        doc.setFontSize(8);
        doc.setTextColor(...negro);
        doc.text('{{ addslashes(mb_substr($inv->producto->nombre ?? "?", 0, 30)) }}', 16, y+5);
        doc.setTextColor(...gris);
        doc.text('{{ addslashes($inv->producto->categoria->nombre ?? "—") }}', 90, y+5);
        doc.setTextColor(...negro);
        doc.text('{{ $inv->lote ?? "—" }}', 135, y+5);
        doc.text('{{ $caducidad ? $caducidad->format("d/m/Y") : "—" }}', 170, y+5);
        doc.setTextColor({{ $estadoR[0] }}, {{ $estadoR[1] }}, {{ $estadoR[2] }});
        doc.text('{{ $estado }}', 205, y+5);
        doc.setTextColor(...negro);
        doc.text('{{ mb_substr($inv->almacen->nombre ?? "—", 0, 20) }}', 235, y+5);
        doc.setFont('helvetica', 'bold');
        doc.text('{{ number_format($inv->cantidad) }}', 283, y+5, { align:'right' });
        doc.setFont('helvetica', 'normal');
        y += 7;
        doc.setDrawColor(...lineC);
        doc.line(14, y, 283, y);
        @endforeach

        // ── Firma ──
        if (!sinFirma && canvasInv) {
            const firmaData = canvasInv.toDataURL('image/png');
            // Verificar que hay algo dibujado (no canvas vacío)
            const pixeles = ctxInv.getImageData(0, 0, canvasInv.width, canvasInv.height).data;
            const hayFirma = pixeles.some(p => p !== 0);
            if (hayFirma) {
                const lastPage = doc.getNumberOfPages();
                doc.setPage(lastPage);
                // Espacio para firma
                if (y > 160) { doc.addPage(); y = 14; }
                y += 6;
                doc.setDrawColor(...lineC);
                doc.line(14, y, 283, y);
                y += 6;
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(9);
                doc.setTextColor(...azul);
                doc.text('FIRMA DE CONFORMIDAD', 14, y);
                y += 5;
                try {
                    doc.addImage(firmaData, 'PNG', 14, y, 70, 22);
                } catch(e) {}
                y += 26;
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8);
                doc.setTextColor(...gris);
                doc.text('{{ auth()->user()->name }}', 14, y);
                doc.text('Responsable de inventario', 14, y + 4);
                doc.text(new Date().toLocaleDateString('es-MX', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' }), 14, y + 8);
            }
        }

        // ── Pie ──
        const totalPags = doc.getNumberOfPages();
        for (let i = 1; i <= totalPags; i++) {
            doc.setPage(i);
            doc.setFillColor(...azul);
            doc.rect(0, 200, 297, 10, 'F');
            doc.setTextColor(255,255,255);
            doc.setFontSize(7);
            doc.text('Dulces Lero Lero · Inventario General · ' + new Date().toLocaleDateString('es-MX'), 148, 206.5, { align:'center' });
            doc.text('Pág. ' + i + ' / ' + totalPags, 283, 206.5, { align:'right' });
        }

        const almacenNombre = '{{ request("almacen_id") ? ($almacenes->firstWhere("id", request("almacen_id"))?->nombre ?? "todos") : "todos" }}';
        doc.save('inventario-' + almacenNombre + '-' + new Date().toISOString().slice(0,10) + '.pdf');
    }
    </script>
</x-app-layout>