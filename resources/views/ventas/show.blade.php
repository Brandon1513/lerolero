<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detalle de Venta #{{ $venta->id }}
        </h2>
    </x-slot>

    @php
        // Filtrar solo productos individuales sin líneas fantasma de promo
        $detallesProductos = $venta->detalles
            ->where('es_cambio', false)
            ->whereNull('promocion_id'); // si tienes esta col, si no, quita esto
        $subtotalProductos = $detallesProductos->sum('subtotal');

        // Calcular subtotal de promos
        $subtotalPromos = $venta->promociones->sum(fn($vp) => $vp->cantidad * $vp->precio_promocion);
    @endphp

    <div class="max-w-5xl py-12 mx-auto">

        {{-- Encabezado --}}
        <div class="p-6 mb-6 bg-white rounded-lg shadow">
            <p><strong>Fecha:</strong> {{ $venta->fecha }}</p>
            <p><strong>Cliente:</strong> {{ $venta->cliente->nombre }}</p>
            <p><strong>Vendedor:</strong> {{ $venta->vendedor->name }}</p>
            <p><strong>Observaciones:</strong> {{ $venta->observaciones ?? '-' }}</p>
            <p><strong>Total:</strong> ${{ number_format($venta->total, 2) }}</p>
        </div>

        {{-- Productos Vendidos --}}
        <div class="mb-8 overflow-hidden bg-white rounded-lg shadow">
            <h3 class="px-6 py-4 text-lg font-bold bg-gray-100">Productos Vendidos</h3>
            @if($detallesProductos->count() > 0)
            <table class="w-full text-sm border border-collapse">
                <thead class="text-left bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Producto</th>
                        <th class="px-4 py-2 border">Cantidad</th>
                        <th class="px-4 py-2 border">Precio Unitario</th>
                        <th class="px-4 py-2 border">Subtotal</th>
                        <th class="px-4 py-2 border">Almacén</th>
                        <th class="px-4 py-2 border">Lote</th>
                        <th class="px-4 py-2 border">Caducidad</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detallesProductos as $detalle)
                        <tr>
                            <td class="px-4 py-2 border">{{ $detalle->producto->nombre }}</td>
                            <td class="px-4 py-2 border">{{ $detalle->cantidad }}</td>
                            <td class="px-4 py-2 border">${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="px-4 py-2 border">${{ number_format($detalle->subtotal, 2) }}</td>
                            <td class="px-4 py-2 border">{{ $detalle->almacen->nombre ?? 'N/A' }}</td>
                            <td class="px-4 py-2 border">{{ $detalle->lote ?? 'N/D' }}</td>
                            <td class="px-4 py-2 border">{{ $detalle->fecha_caducidad ?? 'N/D' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50">
                        <td colspan="3" class="px-4 py-2 text-right border font-semibold">Subtotal productos:</td>
                        <td class="px-4 py-2 border font-semibold">${{ number_format($subtotalProductos, 2) }}</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
            @else
                <p class="px-6 py-4 text-gray-600">No hay productos individuales en esta venta.</p>
            @endif
        </div>

        {{-- Promociones Vendidas --}}
        <div class="mb-8 overflow-hidden bg-white rounded-lg shadow">
            <h3 class="px-6 py-4 text-lg font-bold bg-purple-100 text-purple-800">Promociones Vendidas</h3>
            @if($venta->promociones->count() > 0)
            <table class="w-full text-sm border border-collapse">
                <thead class="text-left bg-purple-50">
                    <tr>
                        <th class="px-4 py-2 border">Promoción</th>
                        <th class="px-4 py-2 border">Cantidad</th>
                        <th class="px-4 py-2 border">Precio Promo</th>
                        <th class="px-4 py-2 border">Subtotal</th>
                        <th class="px-4 py-2 border">Incluye</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($venta->promociones as $vp)
                        <tr>
                            <td class="px-4 py-2 border">{{ $vp->promocion->nombre ?? 'Promoción' }}</td>
                            <td class="px-4 py-2 border">{{ $vp->cantidad }}</td>
                            <td class="px-4 py-2 border">${{ number_format($vp->precio_promocion, 2) }}</td>
                            <td class="px-4 py-2 border">${{ number_format($vp->cantidad * $vp->precio_promocion, 2) }}</td>
                            <td class="px-4 py-2 border">
                                @if($vp->promocion && $vp->promocion->productos->count() > 0)
                                    <ul class="pl-4 list-disc">
                                        @foreach ($vp->promocion->productos as $p)
                                            <li>{{ $p->nombre }} (x{{ ($p->pivot->cantidad ?? 1) * $vp->cantidad }})</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-500">Sin desglose</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-purple-50">
                        <td colspan="3" class="px-4 py-2 text-right border font-semibold">Subtotal promociones:</td>
                        <td class="px-4 py-2 border font-semibold">${{ number_format($subtotalPromos, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            @else
                <p class="px-6 py-4 text-gray-600">No hay promociones registradas en esta venta.</p>
            @endif
        </div>

        {{-- Productos Devueltos / Cambios --}}
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <h3 class="px-6 py-4 text-lg font-bold bg-yellow-100">Productos Devueltos / Cambios</h3>
            @if ($venta->rechazos->count() > 0)
                <table class="w-full text-sm border border-collapse">
                    <thead class="text-left bg-yellow-100">
                        <tr>
                            <th class="px-4 py-2 border">Producto</th>
                            <th class="px-4 py-2 border">Cantidad</th>
                            <th class="px-4 py-2 border">Motivo</th>
                            <th class="px-4 py-2 border">Almacén</th>
                            <th class="px-4 py-2 border">Lote</th>
                            <th class="px-4 py-2 border">Caducidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->rechazos as $rechazo)
                            <tr>
                                <td class="px-4 py-2 border">{{ $rechazo->producto->nombre ?? '-' }}</td>
                                <td class="px-4 py-2 border">{{ $rechazo->cantidad }}</td>
                                <td class="px-4 py-2 border">{{ ucfirst($rechazo->motivo) ?? '-' }}</td>
                                <td class="px-4 py-2 border">{{ $rechazo->almacen->nombre ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">{{ $rechazo->lote ?? 'N/D' }}</td>
                                <td class="px-4 py-2 border">{{ $rechazo->fecha_caducidad ?? 'N/D' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="px-6 py-4 text-gray-600">No hay productos devueltos en esta venta.</p>
            @endif
        </div>
    </div>
</x-app-layout>
