<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Cierre de Ruta - {{ $cierre->vendedor->name }} ({{ \Carbon\Carbon::parse($cierre->fecha)->format('d/m/Y') }})
        </h2>
    </x-slot>

    <div class="py-12 mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8">

        <!-- Resumen -->
        <div class="p-6 space-y-2 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-bold text-gray-700">Resumen de la Ruta</h3>
            <p><strong>Total de Ventas:</strong> ${{ number_format($cierre->total_ventas, 2) }}</p>
            <p><strong>Estatus:</strong> <span class="capitalize">{{ $cierre->estatus }}</span></p>
            @if ($cierre->cerradoPor)
                <p><strong>Cerrado por:</strong> {{ $cierre->cerradoPor->name }}</p>
            @endif
            @if ($cierre->estatus == 'cuadrado' && !is_null($cierre->total_efectivo))
                @php
                    $diferencia = $cierre->total_efectivo - $cierre->total_ventas;
                @endphp

                <div class="mt-4 p-4 rounded-lg 
                    {{ $diferencia == 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    @if ($diferencia == 0)
                        ✅ Efectivo cuadrado correctamente.
                    @elseif ($diferencia < 0)
                        ⚠️ Faltan ${{ number_format(abs($diferencia), 2) }} pesos para cuadrar el efectivo.
                    @else
                        ⚠️ Sobraron ${{ number_format($diferencia, 2) }} pesos en el efectivo entregado.
                    @endif
                </div>
            @endif


            <!-- Botón o mensaje para ver traslado -->
            @if ($cierre->traslado_id)
                <div class="mt-4">
                    <a href="{{ route('traslados.show', $cierre->traslado_id) }}"
                       class="inline-flex items-center px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        Ver Traslado Generado
                    </a>
                </div>
            @else
                <div class="mt-4">
                    <div class="px-4 py-2 text-sm text-yellow-800 bg-yellow-100 rounded-md">
                        No se ha registrado un traslado para este cierre.
                    </div>
                </div>
            @endif
        </div>

        <!-- Inventario Inicial -->
        <div class="p-6 space-y-2 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-bold text-gray-700">Inventario Inicial</h3>
            @if($cierre->inventario_inicial)
                <table class="w-full text-sm border border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Producto</th>
                            <th class="px-4 py-2 border">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cierre->inventario_inicial as $producto)
                            <tr>
                                <td class="px-4 py-2 border">{{ $producto['nombre'] }}</td>
                                <td class="px-4 py-2 border">{{ $producto['cantidad'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">Sin inventario registrado.</p>
            @endif
        </div>

        <!-- Inventario Final -->
        <div class="p-6 space-y-2 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-bold text-gray-700">Inventario Final</h3>
            @if($cierre->inventario_final)
                <table class="w-full text-sm border border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Producto</th>
                            <th class="px-4 py-2 border">Cantidad</th>
                            <th class="px-4 py-2 border">Lote</th>
                            <th class="px-4 py-2 border">Caducidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cierre->inventario_final as $producto)
                            <tr>
                                <td class="px-4 py-2 border">{{ $producto['nombre'] }}</td>
                                <td class="px-4 py-2 border">{{ $producto['cantidad'] }}</td>
                                <td class="px-4 py-2 border">{{ $producto['lote'] ?? 'N/D' }}</td>
                                <td class="px-4 py-2 border">{{ $producto['fecha_caducidad'] ?? 'N/D' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500">Sin inventario final registrado.</p>
            @endif
        </div>

        <!-- Cambios -->
        @if($cierre->cambios)
            <div class="p-6 space-y-2 bg-white rounded-lg shadow">
                <h3 class="mb-4 text-lg font-bold text-gray-700">Productos en Cambio</h3>
                <table class="w-full text-sm border border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Producto</th>
                            <th class="px-4 py-2 border">Cantidad</th>
                            <th class="px-4 py-2 border">Motivo</th>
                            <th class="px-4 py-2 border">Lote</th>
                            <th class="px-4 py-2 border">Caducidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cierre->cambios as $cambio)
                            <tr>
                                <td class="px-4 py-2 border">{{ $cambio['nombre'] }}</td>
                                <td class="px-4 py-2 border">{{ $cambio['cantidad'] }}</td>
                                <td class="px-4 py-2 border">{{ ucfirst($cambio['motivo']) }}</td>
                                <td class="px-4 py-2 border">{{ $cambio['lote'] ?? 'N/D' }}</td>
                                <td class="px-4 py-2 border">{{ $cambio['fecha_caducidad'] ?? 'N/D' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Formulario de cierre -->
        @if ($cierre->estatus == 'pendiente')
            <div class="p-6 space-y-4 bg-white rounded-lg shadow">
                <h3 class="mb-4 text-lg font-bold text-gray-700">Finalizar Cierre de Ruta</h3>

                <form method="POST" action="{{ route('cierres.update', $cierre) }}" onsubmit="return validarEfectivo()">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="total_efectivo" class="block mb-2 text-sm font-medium text-gray-700">Total Efectivo Entregado</label>
                        <input type="number" step="0.01" name="total_efectivo" id="total_efectivo"
                               class="w-full p-2 border rounded" required>
                    </div>

                    <div>
                        <label for="observaciones" class="block mb-2 text-sm font-medium text-gray-700">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="4"
                                  class="w-full p-2 border rounded"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-2 text-white transition-all bg-green-500 rounded-md hover:bg-green-600">
                            Cerrar Ruta
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="p-6 mt-6 text-center bg-green-100 rounded-lg shadow">
                <p class="font-bold text-green-700">Esta ruta ya fue cerrada.</p>
            </div>
        @endif

    </div>

    <script>
        function validarEfectivo() {
            const totalVentas = {{ $cierre->total_ventas }};
            const efectivo = parseFloat(document.getElementById('total_efectivo').value);

            if (efectivo < totalVentas) {
                return confirm('⚠️ El efectivo entregado es menor que el total de ventas. ¿Seguro que quieres continuar?');
            }
            return true;
        }
    </script>
   

</x-app-layout>
