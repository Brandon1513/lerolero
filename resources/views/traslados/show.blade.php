<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Detalle del Traslado #{{ $traslado->id }}
        </h2>
    </x-slot>

    <div class="max-w-5xl py-12 mx-auto sm:px-6 lg:px-8">
        <div class="p-6 mb-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Información del traslado</h3>
            <p><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($traslado->fecha)->format('d/m/Y') }}</p>
            <p><strong>Origen:</strong> {{ $traslado->origen->nombre }}</p>
            <p><strong>Destino:</strong> {{ $traslado->destino->nombre }}</p>
            <p><strong>Observaciones:</strong> {{ $traslado->observaciones ?? 'N/A' }}</p>
        </div>

        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-bold text-gray-800">Productos trasladados</h3>

            <table class="w-full text-sm border border-collapse border-gray-200">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left border">Producto</th>
                        <th class="px-4 py-2 text-center border">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($traslado->detalles as $detalle)
                        <tr>
                            <td class="px-4 py-2 border">{{ $detalle->producto->nombre }}</td>
                            <td class="px-4 py-2 text-center border">{{ $detalle->cantidad }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-4 py-4 text-center text-gray-500">Sin productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
