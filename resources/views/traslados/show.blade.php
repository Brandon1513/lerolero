<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('traslados.index') }}"
                    class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Traslado #{{ $traslado->id }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($traslado->fecha)->format('d/m/Y') }} ·
                        {{ \Carbon\Carbon::parse($traslado->fecha)->diffForHumans() }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
            {{-- Botón PDF --}}
            <button onclick="generarPDF()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-50 text-indigo-600 text-sm font-semibold rounded-lg border border-indigo-200 hover:bg-indigo-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Descargar PDF
            </button>

            {{-- Acción eliminar desde el show --}}
            @if($traslado->puede_eliminar ?? false)
                <form action="{{ route('traslados.destroy', $traslado) }}" method="POST"
                    onsubmit="return confirm('¿Eliminar este traslado? Se revertirá el inventario al almacén origen.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 text-red-600 text-sm font-semibold rounded-lg border border-red-200 hover:bg-red-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar traslado
                    </button>
                </form>
            @endif
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl py-8 mx-auto sm:px-6 lg:px-8">

        {{-- Alertas --}}
        @if(session('error'))
            <div class="flex items-center gap-3 px-4 py-3 mb-4 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm0-4a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

            {{-- ── COLUMNA IZQUIERDA: Info del traslado ── --}}
            <div class="space-y-5">

                {{-- Card: Ruta del traslado --}}
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Ruta del traslado
                        </h3>
                    </div>
                    <div class="p-5">

                        {{-- Origen --}}
                        <div class="flex items-center gap-3 mb-4">
                            @php $tipoOrigen = $traslado->origen->tipo ?? 'general'; @endphp
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                                {{ $tipoOrigen === 'vendedor' ? 'bg-blue-100' : ($tipoOrigen === 'rechazo' ? 'bg-red-100' : 'bg-green-100') }}">
                                <svg class="w-5 h-5 {{ $tipoOrigen === 'vendedor' ? 'text-blue-600' : ($tipoOrigen === 'rechazo' ? 'text-red-600' : 'text-green-600') }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-semibold tracking-wide text-gray-400 uppercase">Origen</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $traslado->origen->nombre }}</div>
                            </div>
                        </div>

                        {{-- Flecha --}}
                        <div class="flex items-center gap-3 pl-1 mb-4">
                            <div class="flex justify-center w-7">
                                <div class="flex flex-col items-center">
                                    <div class="w-px h-3 bg-gray-300"></div>
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    <div class="w-px h-3 bg-gray-300"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Destino --}}
                        <div class="flex items-center gap-3">
                            @php $tipoDestino = $traslado->destino->tipo ?? 'general'; @endphp
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                                {{ $tipoDestino === 'vendedor' ? 'bg-blue-100' : ($tipoDestino === 'rechazo' ? 'bg-red-100' : 'bg-green-100') }}">
                                <svg class="w-5 h-5 {{ $tipoDestino === 'vendedor' ? 'text-blue-600' : ($tipoDestino === 'rechazo' ? 'text-red-600' : 'text-green-600') }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-xs font-semibold tracking-wide text-gray-400 uppercase">Destino</div>
                                <div class="text-sm font-semibold text-gray-900">{{ $traslado->destino->nombre }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card: Realizado por --}}
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Realizado por
                        </h3>
                    </div>
                    <div class="p-5">
                        @if($traslado->usuario)
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 text-sm font-bold text-indigo-700 bg-indigo-100 rounded-full shrink-0">
                                    {{ strtoupper(substr($traslado->usuario->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $traslado->usuario->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $traslado->usuario->email }}</div>
                                </div>
                            </div>
                        @else
                            <span class="text-sm text-gray-400">No registrado</span>
                        @endif

                        {{-- Firma digital --}}
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="text-xs font-semibold tracking-wide text-gray-500 uppercase mb-2">Firma digital</div>
                            @if($traslado->firma_base64)
                                <img src="{{ $traslado->firma_base64 }}" class="border border-gray-200 rounded-lg w-full h-24 object-contain bg-white" alt="Firma"/>
                                <p class="mt-1 text-xs text-green-600 font-semibold">✓ Firmado</p>
                            @else
                                {{-- Canvas para firmar --}}
                                <canvas id="firma-canvas" width="280" height="96"
                                    class="border-2 border-dashed border-gray-300 rounded-lg w-full cursor-crosshair bg-white touch-none"></canvas>
                                <div class="flex gap-2 mt-2">
                                    <button type="button" onclick="limpiarFirma()"
                                        class="flex-1 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                                        Limpiar
                                    </button>
                                    <button type="button" onclick="guardarFirma()"
                                        class="flex-1 py-1.5 text-xs font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                                        Guardar firma
                                    </button>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">Firma con el dedo o el mouse</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Card: Detalles generales --}}
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Detalles
                        </h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold tracking-wide text-gray-500 uppercase">Fecha</span>
                            <span class="text-sm font-semibold text-gray-900">
                                {{ \Carbon\Carbon::parse($traslado->fecha)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold tracking-wide text-gray-500 uppercase">Productos</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                                {{ $traslado->detalles->count() }} tipo(s)
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold tracking-wide text-gray-500 uppercase">Unidades</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                {{ $traslado->detalles->sum('cantidad') }} uds
                            </span>
                        </div>
                        @if($traslado->observaciones)
                            <div class="pt-2 border-t border-gray-100">
                                <div class="mb-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">Observaciones</div>
                                <p class="text-sm text-gray-700">{{ $traslado->observaciones }}</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- ── COLUMNA DERECHA: Tabla de productos (2/3) ── --}}
            <div class="lg:col-span-2">
                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                            Productos trasladados
                        </h3>
                        <span class="text-xs text-gray-400">
                            {{ $traslado->detalles->sum('cantidad') }} unidades en total
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50">
                                    <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Producto</th>
                                    <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Lote</th>
                                    <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Caduca</th>
                                    <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($traslado->detalles as $detalle)
                                    @php
                                        $caducidad = $detalle->fecha_caducidad
                                            ? \Carbon\Carbon::parse($detalle->fecha_caducidad)
                                            : null;
                                        $vencido   = $caducidad && $caducidad->isPast();
                                        $proximoVencer = $caducidad && !$vencido && $caducidad->diffInDays(now()) <= 30;
                                    @endphp
                                    <tr class="transition-colors hover:bg-gray-50">

                                        {{-- Producto --}}
                                        <td class="px-5 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="flex items-center justify-center text-xs font-bold text-indigo-700 bg-indigo-100 rounded-md w-7 h-7 shrink-0">
                                                    {{ strtoupper(substr($detalle->producto->nombre ?? '?', 0, 1)) }}
                                                </div>
                                                <span class="font-medium text-gray-900">
                                                    {{ $detalle->producto->nombre ?? 'Producto #'.$detalle->producto_id }}
                                                </span>
                                            </div>
                                        </td>

                                        {{-- Lote --}}
                                        <td class="px-5 py-3.5">
                                            @if($detalle->lote)
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold rounded bg-gray-100 text-gray-700">
                                                    {{ $detalle->lote }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>

                                        {{-- Caducidad --}}
                                        <td class="px-5 py-3.5">
                                            @if($caducidad)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full
                                                    {{ $vencido        ? 'bg-red-100 text-red-700'     :
                                                      ($proximoVencer  ? 'bg-amber-100 text-amber-700' :
                                                                         'bg-green-100 text-green-700') }}">
                                                    @if($vencido)
                                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                        Vencido
                                                    @elseif($proximoVencer)
                                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    @else
                                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                    @endif
                                                    {{ $caducidad->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">—</span>
                                            @endif
                                        </td>

                                        {{-- Cantidad --}}
                                        <td class="px-5 py-3.5 text-right">
                                            <span class="font-bold text-gray-900">{{ number_format($detalle->cantidad) }}</span>
                                            <span class="text-xs text-gray-400 ml-0.5">uds</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-12 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                                </svg>
                                                <span class="text-sm text-gray-500">Sin productos registrados</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            {{-- Pie con total --}}
                            @if($traslado->detalles->count() > 0)
                                <tfoot>
                                    <tr class="border-t-2 border-gray-200 bg-gray-50">
                                        <td colspan="3" class="px-5 py-3 text-sm font-semibold text-right text-gray-600">
                                            Total unidades:
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <span class="text-base font-bold text-indigo-700">
                                                {{ number_format($traslado->detalles->sum('cantidad')) }}
                                            </span>
                                            <span class="text-xs text-gray-400 ml-0.5">uds</span>
                                        </td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
    // ── Firma digital ──────────────────────────────────────────────────
    const canvas = document.getElementById('firma-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        let drawing = false;

        const getPos = (e) => {
            const r = canvas.getBoundingClientRect();
            const src = e.touches ? e.touches[0] : e;
            return { x: src.clientX - r.left, y: src.clientY - r.top };
        };

        canvas.addEventListener('mousedown',  e => { drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
        canvas.addEventListener('mousemove',  e => { if (!drawing) return; const p = getPos(e); ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#1e1b4b'; ctx.lineTo(p.x, p.y); ctx.stroke(); });
        canvas.addEventListener('mouseup',    () => drawing = false);
        canvas.addEventListener('mouseleave', () => drawing = false);

        canvas.addEventListener('touchstart',  e => { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }, { passive: false });
        canvas.addEventListener('touchmove',   e => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#1e1b4b'; ctx.lineTo(p.x, p.y); ctx.stroke(); }, { passive: false });
        canvas.addEventListener('touchend',    () => drawing = false);
    }

    function limpiarFirma() {
        if (!canvas) return;
        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
    }

    async function guardarFirma() {
        if (!canvas) return;
        const firmaData = canvas.toDataURL('image/png');

        const res = await fetch('{{ route("traslados.firma", $traslado->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ firma_base64: firmaData })
        });

        if (res.ok) {
            location.reload();
        } else {
            alert('Error al guardar la firma');
        }
    }

    // ── Generar PDF ────────────────────────────────────────────────────
    async function generarPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

        const azul   = [67, 56, 202];
        const gris   = [107, 114, 128];
        const negro  = [17, 24, 39];
        const lineC  = [229, 231, 235];

        let y = 20;

        // ── Encabezado ──
        doc.setFillColor(...azul);
        doc.rect(0, 0, 210, 14, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(11);
        doc.setFont('helvetica', 'bold');
        doc.text('Dulces Lero Lero — Comprobante de Traslado', 14, 9.5);

        // ── Título ──
        y = 24;
        doc.setTextColor(...negro);
        doc.setFontSize(16);
        doc.text('Traslado #{{ $traslado->id }}', 14, y);
        doc.setFontSize(9);
        doc.setTextColor(...gris);
        doc.text('Fecha: {{ \Carbon\Carbon::parse($traslado->fecha)->format("d/m/Y") }}', 14, y + 6);
        doc.text('Generado: ' + new Date().toLocaleDateString('es-MX'), 14, y + 11);

        // ── Línea divisora ──
        y += 18;
        doc.setDrawColor(...lineC);
        doc.line(14, y, 196, y);
        y += 6;

        // ── Ruta del traslado ──
        doc.setFontSize(10);
        doc.setFont('helvetica', 'bold');
        doc.setTextColor(...azul);
        doc.text('RUTA DEL TRASLADO', 14, y);
        y += 5;
        doc.setTextColor(...negro);
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.text('Origen:  {{ $traslado->origen->nombre }}', 14, y);
        y += 5;
        doc.text('Destino: {{ $traslado->destino->nombre }}', 14, y);
        @if($traslado->observaciones)
        y += 5;
        doc.text('Obs.: {{ $traslado->observaciones }}', 14, y);
        @endif

        // ── Realizado por ──
        y += 8;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.setTextColor(...azul);
        doc.text('REALIZADO POR', 14, y);
        y += 5;
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.setTextColor(...negro);
        doc.text('{{ $traslado->usuario->name ?? "No registrado" }}', 14, y);

        // ── Línea divisora ──
        y += 8;
        doc.setDrawColor(...lineC);
        doc.line(14, y, 196, y);
        y += 6;

        // ── Tabla de productos ──
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.setTextColor(...azul);
        doc.text('PRODUCTOS TRASLADADOS', 14, y);
        y += 5;

        // Cabecera tabla
        doc.setFillColor(243, 244, 246);
        doc.rect(14, y, 182, 7, 'F');
        doc.setFontSize(8);
        doc.setTextColor(...gris);
        doc.setFont('helvetica', 'bold');
        doc.text('PRODUCTO', 16, y + 5);
        doc.text('CATEGORÍA', 90, y + 5);
        doc.text('LOTE', 125, y + 5);
        doc.text('CADUCA', 155, y + 5);
        doc.text('CANT.', 183, y + 5, { align: 'right' });
        y += 7;

        // Filas
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(...negro);
        @foreach($traslado->detalles as $detalle)
        // Salto de página automático
        if (y > 250) {
            doc.addPage();
            y = 14;
            // Re-dibujar cabecera de tabla en página nueva
            doc.setFillColor(243, 244, 246);
            doc.rect(14, y, 182, 7, 'F');
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(8);
            doc.setTextColor(...gris);
            doc.text('PRODUCTO', 16, y + 5);
            doc.text('CATEGORÍA', 90, y + 5);
            doc.text('LOTE', 125, y + 5);
            doc.text('CADUCA', 155, y + 5);
            doc.text('CANT.', 183, y + 5, { align: 'right' });
            y += 7;
            doc.setFont('helvetica', 'normal');
            doc.setTextColor(...negro);
        }
        doc.setFontSize(8);
        doc.text('{{ addslashes($detalle->producto->nombre ?? "?") }}', 16, y + 5);
        doc.setTextColor(...gris);
        doc.text('{{ addslashes($detalle->producto->categoria->nombre ?? "—") }}', 90, y + 5);
        doc.setTextColor(...negro);
        doc.text('{{ $detalle->lote ?? "—" }}', 125, y + 5);
        doc.text('{{ $detalle->fecha_caducidad ? \Carbon\Carbon::parse($detalle->fecha_caducidad)->format("d/m/Y") : "—" }}', 155, y + 5);
        doc.text('{{ $detalle->cantidad }}', 194, y + 5, { align: 'right' });
        y += 7;
        doc.setDrawColor(...lineC);
        doc.line(14, y, 196, y);
        @endforeach

        // Total
        y += 3;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9);
        doc.setTextColor(...azul);
        doc.text('Tipos de producto:', 100, y + 5);
        doc.text('{{ $traslado->detalles->count() }}', 155, y + 5, { align: 'right' });
        doc.text('Total unidades:', 100, y + 11);
        doc.text('{{ $traslado->detalles->sum("cantidad") }}', 155, y + 11, { align: 'right' });
        y += 8;

        // ── Firma ──
        y += 14;
        doc.setDrawColor(...lineC);
        doc.line(14, y, 196, y);
        y += 8;

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.setTextColor(...azul);
        doc.text('FIRMA DE CONFORMIDAD', 14, y);
        y += 6;

        @if($traslado->firma_base64)
        try {
            doc.addImage('{{ $traslado->firma_base64 }}', 'PNG', 14, y, 60, 20);
        } catch(e) {}
        y += 24;
        @else
        // Espacio en blanco para firma manual
        doc.setDrawColor(200, 200, 200);
        doc.rect(14, y, 80, 20);
        y += 24;
        @endif

        doc.setFont('helvetica', 'normal');
        doc.setFontSize(8);
        doc.setTextColor(...gris);
        doc.text('{{ $traslado->usuario->name ?? "___________________________" }}', 14, y);
        doc.text('Nombre y firma', 14, y + 4);

        // ── Pie ──
        doc.setFillColor(...azul);
        doc.rect(0, 287, 210, 10, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(7);
        doc.text('Dulces Lero Lero · Traslado #{{ $traslado->id }} · {{ \Carbon\Carbon::parse($traslado->fecha)->format("d/m/Y") }}', 105, 293.5, { align: 'center' });

        doc.save('traslado-{{ $traslado->id }}.pdf');
    }
    </script>
</x-app-layout>