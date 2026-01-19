<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Lista de Cierres de Ruta
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Filtros -->
                    <form method="GET" action="{{ route('cierres.index') }}" class="flex flex-wrap items-end gap-4 mb-4">
                        
                        <!-- Filtro por Vendedor -->
                        <div>
                            <x-input-label for="vendedor_id" :value="__('Vendedor')" />
                            <select name="vendedor_id" id="vendedor_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Todos --</option>
                                @foreach ($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}" {{ (string) request('vendedor_id') === (string) $vendedor->id ? 'selected' : '' }}>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filtro por Fecha Inicio -->
                        <div>
                            <x-input-label for="fecha_inicio" :value="__('Desde')" />
                            <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="block w-full mt-1"
                                value="{{ request('fecha_inicio') }}" />
                        </div>

                        <!-- Filtro por Fecha Fin -->
                        <div>
                            <x-input-label for="fecha_fin" :value="__('Hasta')" />
                            <x-text-input id="fecha_fin" name="fecha_fin" type="date" class="block w-full mt-1"
                                value="{{ request('fecha_fin') }}" />
                        </div>

                        <!-- Filtro por Estatus -->
                        <div>
                            <x-input-label for="estatus" :value="__('Estatus')" />
                            <select name="estatus" id="estatus" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Todos --</option>
                                <option value="pendiente" {{ request('estatus') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="cuadrado" {{ request('estatus') == 'cuadrado' ? 'selected' : '' }}>Cuadrado</option>
                            </select>
                        </div>

                        <!-- Filtro por Cerrado Por -->
                        <div>
                            <x-input-label for="cerrado_por" :value="__('Cerrado por')" />
                            <select name="cerrado_por" id="cerrado_por" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Todos --</option>
                                @foreach ($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ (string) request('cerrado_por') === (string) $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Botones -->
                        <div class="flex items-end gap-2">
                            <x-primary-button class="h-[42px]">
                                {{ __('Filtrar') }}
                            </x-primary-button>

                            <a href="{{ route('cierres.index') }}"
                               class="h-[42px] px-4 py-2 text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md flex items-center">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <!-- Contador de resultados -->
                    @if ($cierres->count() > 0)
                        <p class="mb-2 text-sm text-gray-600">
                            Mostrando <span class="font-semibold">{{ $cierres->count() }}</span> cierre{{ $cierres->count() > 1 ? 's' : '' }}.
                        </p>
                    @else
                        <p class="mb-2 text-sm text-red-600">
                            No se encontraron cierres con los filtros aplicados.
                        </p>
                    @endif

                    <!-- Tabla de Cierres -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border border-collapse border-gray-200">
                            <thead class="text-gray-700 bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 border">Vendedor</th>
                                    <th class="px-4 py-2 border">Fecha</th>
                                    <th class="px-4 py-2 border">Total Ventas</th>
                                    <th class="px-4 py-2 border">Estatus</th>
                                    <th class="px-4 py-2 border">Cerrado Por</th>
                                    <th class="px-4 py-2 text-center border">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cierres as $cierre)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $cierre->vendedor->name }}</td>
                                        <td class="px-4 py-2 border">{{ \Carbon\Carbon::parse($cierre->fecha)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2 border">${{ number_format($cierre->total_ventas, 2) }}</td>
                                        <td class="px-4 py-2 capitalize border">{{ $cierre->estatus }}</td>
                                        <td class="px-4 py-2 border">
                                            {{ $cierre->cerradoPor ? $cierre->cerradoPor->name : '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-center border">
                                            <div class="flex flex-wrap justify-center gap-2">
                                                <a href="{{ route('cierres.show', $cierre) }}"
                                                    class="px-3 py-1 text-white bg-blue-500 rounded-md hover:bg-blue-700">
                                                    Ver Detalle
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Paginación -->
                        <div class="mt-4">
                            {{ $cierres->links() }}
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
    @if (session('toast'))
    <div id="toast" class="fixed bottom-4 right-4 z-50 p-4 rounded shadow-md text-white transition-all duration-300
        {{ session('toast') === 'cuadrado' ? 'bg-green-600' : 'bg-yellow-500' }}">
        @switch(session('toast'))
            @case('cuadrado')
                ✅ Efectivo cuadrado correctamente.
                @break
            @case('faltan')
                ⚠️ Faltan pesos en el efectivo entregado.
                @break
            @case('sobran')
                ⚠️ Sobraron pesos en el efectivo entregado.
                @break
        @endswitch
    </div>

    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast');
            if (toast) toast.style.opacity = '0';
        }, 3500);
    </script>
@endif

</x-app-layout>
