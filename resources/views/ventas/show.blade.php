<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('ventas.panel') }}"
                    class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Detalle de Venta <span class="text-indigo-600">#{{ $venta->id }}</span></h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }} · {{ $venta->cliente->nombre }}</p>
                </div>
            </div>
            <span class="inline-flex items-center px-3 py-1.5 text-sm font-bold rounded-xl bg-green-100 text-green-800">
                Total: ${{ number_format($venta->total, 2) }}
            </span>
        </div>
    </x-slot>

    @php
        $detallesProductos = $venta->detalles->where('es_cambio', false)->whereNull('promocion_id');
        $subtotalProductos = $detallesProductos->sum('subtotal');
        $subtotalPromos    = $venta->promociones->sum(fn($vp) => $vp->cantidad * $vp->precio_promocion);
    @endphp

    <div class="max-w-5xl py-8 mx-auto space-y-5 sm:px-6 lg:px-8">

        {{-- ── ENCABEZADO ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Información de la venta
                </h3>
            </div>
            <div class="grid grid-cols-2 gap-4 p-5 sm:grid-cols-4">
                <div>
                    <div class="mb-1 text-xs font-semibold tracking-wide text-gray-400 uppercase">Fecha</div>
                    <div class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($venta->fecha)->format('d/m/Y') }}</div>
                    <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($venta->fecha)->diffForHumans() }}</div>
                </div>
                <div>
                    <div class="mb-1 text-xs font-semibold tracking-wide text-gray-400 uppercase">Cliente</div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center text-xs font-bold text-indigo-700 bg-indigo-100 rounded-full w-7 h-7 shrink-0">
                            {{ strtoupper(substr($venta->cliente->nombre, 0, 1)) }}
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $venta->cliente->nombre }}</span>
                    </div>
                </div>
                <div>
                    <div class="mb-1 text-xs font-semibold tracking-wide text-gray-400 uppercase">Vendedor</div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center text-xs font-bold text-blue-700 bg-blue-100 rounded-full w-7 h-7 shrink-0">
                            {{ strtoupper(substr($venta->vendedor->name, 0, 1)) }}
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $venta->vendedor->name }}</span>
                    </div>
                </div>
                <div>
                    <div class="mb-1 text-xs font-semibold tracking-wide text-gray-400 uppercase">Total</div>
                    <div class="text-xl font-bold text-gray-900">${{ number_format($venta->total, 2) }}</div>
                </div>
                @if($venta->observaciones)
                <div class="col-span-2 sm:col-span-4">
                    <div class="mb-1 text-xs font-semibold tracking-wide text-gray-400 uppercase">Observaciones</div>
                    <div class="flex items-start gap-2 px-3 py-2 text-sm border rounded-lg bg-amber-50 border-amber-200 text-amber-800">
                        <svg class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $venta->observaciones }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── PRODUCTOS VENDIDOS ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                    Productos Vendidos
                </h3>
                <span class="text-xs font-semibold text-indigo-700 bg-indigo-100 px-2.5 py-0.5 rounded-full">
                    {{ $detallesProductos->count() }} producto{{ $detallesProductos->count() != 1 ? 's' : '' }}
                </span>
            </div>

            @if($detallesProductos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50">
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Producto</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Cantidad</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Precio Unit.</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Subtotal</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Almacén</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Lote</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Caducidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($detallesProductos as $detalle)
                                <tr class="transition-colors hover:bg-gray-50">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="flex items-center justify-center text-xs font-bold text-indigo-700 bg-indigo-100 rounded-lg w-7 h-7 shrink-0">
                                                {{ strtoupper(substr($detalle->producto->nombre, 0, 1)) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $detalle->producto->nombre }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-gray-700 bg-gray-100 rounded-full">
                                            {{ $detalle->cantidad }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-gray-700">${{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="px-5 py-3.5 text-right font-bold text-gray-900">${{ number_format($detalle->subtotal, 2) }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $detalle->almacen->nombre ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        @if($detalle->lote)
                                            <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-600">{{ $detalle->lote }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">N/D</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-sm text-gray-600">
                                        {{ $detalle->fecha_caducidad ?? 'N/D' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-indigo-200 bg-indigo-50">
                                <td colspan="3" class="px-5 py-3 text-sm font-semibold text-right text-indigo-700">Subtotal productos:</td>
                                <td class="px-5 py-3 font-bold text-right text-indigo-900">${{ number_format($subtotalProductos, 2) }}</td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="px-5 py-10 text-sm text-center text-gray-400">No hay productos individuales en esta venta.</div>
            @endif
        </div>

        {{-- ── PROMOCIONES VENDIDAS ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-purple-100 bg-purple-50">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-purple-800">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                    Promociones Vendidas
                </h3>
                <span class="text-xs font-semibold text-purple-700 bg-purple-100 px-2.5 py-0.5 rounded-full">
                    {{ $venta->promociones->count() }} promo{{ $venta->promociones->count() != 1 ? 's' : '' }}
                </span>
            </div>

            @if($venta->promociones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-purple-100 bg-purple-50">
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-purple-600 uppercase">Promoción</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-purple-600 uppercase">Cantidad</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-purple-600 uppercase">Precio Promo</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-purple-600 uppercase">Subtotal</th>
                                <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-purple-600 uppercase">Incluye</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($venta->promociones as $vp)
                                <tr class="transition-colors hover:bg-purple-50/40">
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div class="flex items-center justify-center text-xs font-bold text-purple-700 bg-purple-100 rounded-lg w-7 h-7 shrink-0">P</div>
                                            <span class="font-medium text-gray-900">{{ $vp->promocion->nombre ?? 'Promoción' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3.5 text-center">
                                        <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-purple-700 bg-purple-100 rounded-full">
                                            {{ $vp->cantidad }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right text-gray-700">${{ number_format($vp->precio_promocion, 2) }}</td>
                                    <td class="px-5 py-3.5 text-right font-bold text-gray-900">${{ number_format($vp->cantidad * $vp->precio_promocion, 2) }}</td>
                                    <td class="px-5 py-3.5">
                                        @if($vp->promocion && $vp->promocion->productos->count() > 0)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($vp->promocion->productos as $p)
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-indigo-100 text-indigo-700">
                                                        {{ $p->nombre }} ×{{ ($p->pivot->cantidad ?? 1) * $vp->cantidad }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">Sin desglose</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-purple-200 bg-purple-50">
                                <td colspan="3" class="px-5 py-3 text-sm font-semibold text-right text-purple-700">Subtotal promociones:</td>
                                <td class="px-5 py-3 font-bold text-right text-purple-900">${{ number_format($subtotalPromos, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="px-5 py-8 text-sm text-center text-gray-400">No hay promociones registradas en esta venta.</div>
            @endif
        </div>

        {{-- ── PRODUCTOS DEVUELTOS / CAMBIOS ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-amber-100 bg-amber-50">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-amber-800">
                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    Cambios / Devoluciones
                </h3>
                <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2.5 py-0.5 rounded-full">
                    {{ $venta->rechazos->count() }} cambio{{ $venta->rechazos->count() != 1 ? 's' : '' }}
                </span>
            </div>

            @if($venta->rechazos->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($venta->rechazos as $rechazo)
                        <div class="p-5">

                            {{-- ── Fila: producto devuelto → productos dados a cambio ── --}}
                            <div class="flex flex-col items-start gap-4 sm:flex-row">

                                {{-- LADO IZQUIERDO: producto devuelto --}}
                                <div class="flex-1 min-w-0 p-4 border border-red-200 bg-red-50 rounded-xl">
                                    <div class="flex items-center gap-1.5 mb-3">
                                        <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                                        <span class="text-xs font-bold tracking-wide text-red-600 uppercase">Cliente devuelve</span>
                                    </div>
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex items-center justify-center text-sm font-bold text-red-700 bg-red-100 w-9 h-9 rounded-xl shrink-0">
                                            {{ strtoupper(substr($rechazo->producto->nombre ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 truncate">{{ $rechazo->producto->nombre ?? '—' }}</div>
                                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                                    ×{{ $rechazo->cantidad }}
                                                </span>
                                                @if($rechazo->lote)
                                                    <span class="font-mono text-xs bg-white px-1.5 py-0.5 rounded border border-red-200 text-gray-500">{{ $rechazo->lote }}</span>
                                                @endif
                                                @if($rechazo->fecha_caducidad)
                                                    <span class="text-xs text-gray-500">Cad: {{ \Carbon\Carbon::parse($rechazo->fecha_caducidad)->format('d/m/Y') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            {{ ucfirst($rechazo->motivo ?? 'Sin motivo') }}
                                        </span>
                                        @if($rechazo->almacen)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                                                → {{ $rechazo->almacen->nombre }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- FLECHA ── --}}
                                <div class="flex items-center self-center justify-center shrink-0">
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-100 border border-gray-200 rounded-full">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                </div>

                                {{-- LADO DERECHO: productos dados a cambio --}}
                                <div class="flex-1 min-w-0 p-4 border border-green-200 bg-green-50 rounded-xl">
                                    <div class="flex items-center gap-1.5 mb-3">
                                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                        <span class="text-xs font-bold tracking-wide text-green-600 uppercase">Se entrega a cambio</span>
                                    </div>

                                    @if($rechazo->detalles && $rechazo->detalles->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($rechazo->detalles as $detalle)
                                                <div class="flex items-center gap-2.5 bg-white rounded-lg p-2.5 border border-green-100">
                                                    <div class="flex items-center justify-center w-8 h-8 text-xs font-bold text-green-700 bg-green-100 rounded-lg shrink-0">
                                                        {{ strtoupper(substr($detalle->producto->nombre ?? '?', 0, 1)) }}
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $detalle->producto->nombre ?? '—' }}</div>
                                                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                                            <span class="text-xs font-semibold text-green-700 bg-green-100 px-1.5 py-0.5 rounded-full">×{{ $detalle->cantidad }}</span>
                                                            @if($detalle->lote)
                                                                <span class="font-mono text-xs bg-gray-50 px-1.5 py-0.5 rounded border border-gray-200 text-gray-500">{{ $detalle->lote }}</span>
                                                            @endif
                                                            @if($detalle->fecha_caducidad)
                                                                <span class="text-xs text-gray-400">Cad: {{ \Carbon\Carbon::parse($detalle->fecha_caducidad)->format('d/m/Y') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="flex items-center gap-2 py-2 text-sm text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Sin sustituto registrado
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-8 text-sm text-center text-gray-400">No hay cambios o devoluciones en esta venta.</div>
            @endif
        </div>


                {{-- ── RESUMEN FINAL ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">Resumen</h3>
            </div>
            <div class="p-5">
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                        <dt class="text-gray-500">Subtotal productos</dt>
                        <dd class="font-semibold text-gray-900">${{ number_format($subtotalProductos, 2) }}</dd>
                    </div>
                    @if($subtotalPromos > 0)
                    <div class="flex justify-between items-center py-1.5 border-b border-gray-100">
                        <dt class="text-gray-500">Subtotal promociones</dt>
                        <dd class="font-semibold text-gray-900">${{ number_format($subtotalPromos, 2) }}</dd>
                    </div>
                    @endif
                    <div class="flex items-center justify-between py-2 mt-1">
                        <dt class="text-base font-bold text-gray-900">Total de la venta</dt>
                        <dd class="text-xl font-bold text-indigo-700">${{ number_format($venta->total, 2) }}</dd>
                    </div>
                </dl>
            </div>
        </div>

    </div>
</x-app-layout>