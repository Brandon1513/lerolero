<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Detalle de Venta #{{ $venta->id }}
        </h2>
    </x-slot>

    <div class="max-w-5xl py-12 mx-auto">
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
                    @foreach ($venta->detalles->where('es_cambio', false) as $detalle)
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
            </table>
        </div>

        {{-- Productos Devueltos / Cambios --}}
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <h3 class="px-6 py-4 text-lg font-bold bg-yellow-100">Productos Devueltos / Cambios</h3>

            @if ($venta->detalles->where('es_cambio', true)->count() > 0)
                <table class="w-full text-sm border border-collapse">
                    <thead class="text-left bg-yellow-100">
                        <tr>
                            <th class="px-4 py-2 border">Producto</th>
                            <th class="px-4 py-2 border">Cantidad</th>
                            <th class="px-4 py-2 border">Motivo de Cambio</th>
                            <th class="px-4 py-2 border">Almacén</th>
                            <th class="px-4 py-2 border">Lote</th>
                            <th class="px-4 py-2 border">Caducidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($venta->detalles->where('es_cambio', true) as $detalle)
                            <tr>
                                <td class="px-4 py-2 border">{{ $detalle->producto->nombre }}</td>
                                <td class="px-4 py-2 border">{{ $detalle->cantidad }}</td>
                                <td class="px-4 py-2 border">{{ ucfirst($detalle->motivo_cambio) ?? '-' }}</td>
                                <td class="px-4 py-2 border">{{ $detalle->almacen->nombre ?? 'N/A' }}</td>
                                <td class="px-4 py-2 border">{{ $detalle->lote ?? 'N/D' }}</td>
                                <td class="px-4 py-2 border">{{ $detalle->fecha_caducidad ?? 'N/D' }}</td>
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
