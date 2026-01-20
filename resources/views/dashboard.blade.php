<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                 Dashboard - Panel de Control
            </h2>

            <!-- Filtro de fechas -->
            <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2 items-center">
                <input
                    type="date"
                    name="fecha_inicio"
                    value="{{ $fechaInicio }}"
                    class="rounded-md border-gray-300 shadow-sm"
                >
                <span class="text-gray-600">a</span>
                <input
                    type="date"
                    name="fecha_fin"
                    value="{{ $fechaFin }}"
                    class="rounded-md border-gray-300 shadow-sm"
                >
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Filtrar
                </button>
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800">
                    Limpiar
                </a>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- ========================================
                 TARJETAS PRINCIPALES
            ======================================== -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-6 mb-6">

                <!-- Ventas Total -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-blue-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Ventas Totales</dt>
                                    <dd class="flex items-baseline">
                                        <div class="text-2xl font-semibold text-gray-900">
                                            ${{ number_format($totalVentas, 2) }}
                                        </div>
                                        @if($crecimiento != 0)
                                            <div class="ml-2 flex items-baseline text-sm font-semibold {{ $crecimiento >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $crecimiento >= 0 ? '↑' : '↓' }} {{ abs(round($crecimiento, 1)) }}%
                                            </div>
                                        @endif
                                    </dd>
                                    <dd class="text-xs text-gray-500 mt-1">{{ $cantidadVentas }} ventas</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-blue-50 px-6 py-3">
                        <a href="{{ route('ventas.index') }}" class="text-sm text-blue-700 font-medium hover:text-blue-900">
                            Ver todas las ventas →
                        </a>
                    </div>
                </div>

                <!-- Ventas Hoy -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-green-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Ventas Hoy</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">
                                        ${{ number_format($ventasHoy, 2) }}
                                    </dd>
                                    <dd class="text-xs text-gray-500 mt-1">{{ $cantidadVentasHoy }} ventas</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-green-50 px-6 py-3">
                        <span class="text-sm text-green-700 font-medium">
                            Ticket promedio: ${{ number_format($ticketPromedio, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Saldo Pendiente -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-yellow-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Saldo Pendiente</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">
                                        ${{ number_format($saldoPendienteTotal, 2) }}
                                    </dd>
                                    <dd class="text-xs text-gray-500 mt-1">{{ $ventasCredito + $ventasParciales }} ventas crédito</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 px-6 py-3 text-xs text-gray-600">
                        Crédito: {{ $ventasCredito }} | Parcial: {{ $ventasParciales }}
                    </div>
                </div>

                <!-- Inventario (pro) -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-purple-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>

                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Valor Inventario</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">
                                        ${{ number_format($valorInventario, 2) }}
                                    </dd>
                                    <dd class="text-xs text-gray-500 mt-1">
                                        {{ number_format($productosConExistencia) }} productos con existencia ·
                                        {{ number_format($unidadesTotalesInventario) }} unidades
                                    </dd>
                                </dl>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="bg-purple-50 rounded-lg p-3">
                                <div class="text-xs text-gray-600">Lotes agotados</div>
                                <div class="text-lg font-bold text-gray-900">{{ number_format($lotesAgotados) }}</div>
                            </div>
                            <div class="bg-red-50 rounded-lg p-3">
                                <div class="text-xs text-gray-600">Próximos a caducar (30 días)</div>
                                <div class="text-lg font-bold text-red-700">{{ number_format($productosProximosCaducar) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 px-6 py-3">
                        <a href="{{ route('inventarios.index') }}" class="text-sm text-purple-700 font-medium hover:text-purple-900">
                            Ver inventario →
                        </a>
                    </div>
                </div>

                <!-- Productos Activos vs Inactivos -->
                <div class="bg-white overflow-hidden shadow-lg rounded-lg border-l-4 border-gray-500">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gray-700 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Productos</dt>
                                    <dd class="text-2xl font-semibold text-gray-900">
                                        {{ number_format($productosTotal) }}
                                    </dd>
                                    <dd class="text-xs text-gray-500 mt-1">
                                        <span class="text-green-700 font-semibold">{{ number_format($productosActivos) }}</span> activos ·
                                        <span class="text-red-700 font-semibold">{{ number_format($productosInactivos) }}</span> inactivos
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3">
                        <a href="{{ route('productos.index') }}" class="text-sm text-gray-700 font-medium hover:text-gray-900">
                            Administrar productos →
                        </a>
                    </div>
                </div>

            </div>

            <!-- ========================================
                 ACCESOS RÁPIDOS
            ======================================== -->
            <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4"> Accesos Rápidos</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">

                    <a href="{{ route('ventas.create') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
                        <svg class="h-8 w-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Nueva Venta</span>
                    </a>

                    <a href="{{ route('clientes.index') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                        <svg class="h-8 w-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Clientes</span>
                    </a>

                    <a href="{{ route('productos.index') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
                        <svg class="h-8 w-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Productos</span>
                    </a>

                    <a href="{{ route('inventarios.index') }}" class="flex flex-col items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition">
                        <svg class="h-8 w-8 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Inventario</span>
                    </a>

                    <a href="{{ route('vendedores.index') }}" class="flex flex-col items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition">
                        <svg class="h-8 w-8 text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Vendedores</span>
                    </a>

                    <a href="{{ route('cierres.index') }}" class="flex flex-col items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition">
                        <svg class="h-8 w-8 text-red-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Cierres Ruta</span>
                    </a>

                </div>
            </div>

            <!-- ========================================
                 GRÁFICOS Y ESTADÍSTICAS
            ======================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

                <!-- Ventas por Día -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4"> Ventas por Día (últimos 30 días)</h3>
                    <canvas id="ventasPorDiaChart"></canvas>
                </div>

                <!-- Ventas por Categoría -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4"> Ventas por Categoría</h3>
                    <canvas id="ventasPorCategoriaChart"></canvas>
                </div>

                <!-- Inventario por Almacén (Chart) -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4"> Inventario por Almacén</h3>
                    <canvas id="inventarioPorAlmacenChart"></canvas>
                </div>

            </div>

            <!-- ========================================
                 TOP 10 PRÓXIMOS A CADUCAR
            ======================================== -->
            @php
                // Conserva el resto de filtros (fechas, etc.) al cambiar caducidad
                $baseQuery = request()->except('caducan_en');
            @endphp

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900"> Próximos a caducar</h3>
                    <p class="text-sm text-gray-500">Mostrando Top 10 que caducan en ≤ {{ $caducanEn }} días.</p>
                </div>

                <div class="flex gap-2">
                    @foreach([7, 15, 30] as $d)
                        @php $isActive = (int)$caducanEn === $d; @endphp
                        <a
                            href="{{ route('dashboard', array_merge($baseQuery, ['caducan_en' => $d])) }}"
                            class="px-3 py-2 rounded-md text-sm font-semibold border transition
                                {{ $isActive ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}"
                        >
                            {{ $d }} días
                        </a>
                    @endforeach

                    <a
                        href="{{ route('dashboard', $baseQuery) }}"
                        class="px-3 py-2 rounded-md text-sm font-semibold border bg-white text-gray-700 border-gray-300 hover:bg-gray-50"
                        title="Quitar filtro"
                    >
                        Limpiar
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-red-600">
                    <h3 class="text-lg font-semibold text-white"> Top 10 Próximos a Caducar</h3>
                </div>

                <div class="p-6">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Almacén</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Caducidad</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Días restantes</th>
                                <th class="text-right text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($topCaducidad as $row)
                            @php
                                $dias = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($row->fecha_caducidad), false);

                                $badgeClass = $dias < 0
                                    ? 'bg-gray-100 text-gray-700'
                                    : ($dias <= 7
                                        ? 'bg-red-100 text-red-800'
                                        : ($dias <= 30
                                            ? 'bg-yellow-100 text-yellow-800'
                                            : 'bg-green-100 text-green-800'));

                                $diasLabel = $dias < 0 ? 'Vencido' : $dias . ' días';

                                // URLs seguras (si la ruta no existe, cae a index)
                                $productoUrl =
                                    \Illuminate\Support\Facades\Route::has('productos.show')
                                        ? route('productos.show', $row->producto_id)
                                        : (\Illuminate\Support\Facades\Route::has('productos.edit')
                                            ? route('productos.edit', $row->producto_id)
                                            : route('productos.index'));

                                $almacenUrl =
                                    \Illuminate\Support\Facades\Route::has('almacenes.show')
                                        ? route('almacenes.show', $row->almacen_id)
                                        : (\Illuminate\Support\Facades\Route::has('almacenes.edit')
                                            ? route('almacenes.edit', $row->almacen_id)
                                            : route('almacenes.index'));
                            @endphp

                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 text-sm font-medium">
                                    <a href="{{ $productoUrl }}" class="text-blue-700 hover:text-blue-900 hover:underline">
                                        {{ $row->producto }}
                                    </a>
                                </td>

                                <td class="py-3 text-sm text-gray-700">{{ $row->lote }}</td>

                                <td class="py-3 text-sm">
                                    <a href="{{ $almacenUrl }}" class="text-indigo-700 hover:text-indigo-900 hover:underline">
                                        {{ $row->almacen }}
                                    </a>
                                </td>

                                <td class="py-3 text-sm text-gray-700">
                                    {{ \Carbon\Carbon::parse($row->fecha_caducidad)->format('Y-m-d') }}
                                </td>

                                <td class="py-3 text-sm">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                        {{ $diasLabel }}
                                    </span>
                                </td>

                                <td class="py-3 text-sm text-right font-semibold text-gray-900">
                                    {{ number_format($row->cantidad) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500">Sin datos</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ========================================
                 TABLAS DE RANKINGS
            ======================================== -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

                <!-- Top Vendedores -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600">
                        <h3 class="text-lg font-semibold text-white"> Top Vendedores</h3>
                    </div>
                    <div class="p-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Vendedor</th>
                                    <th class="text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topVendedores as $index => $vendedor)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 text-sm font-bold text-gray-900">{{ $index + 1 }}</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            {{ $vendedor['nombre'] }}
                                            <div class="text-xs text-gray-500">{{ $vendedor['ventas'] }} ventas</div>
                                        </td>
                                        <td class="py-3 text-sm text-right font-semibold text-gray-900">
                                            ${{ number_format($vendedor['total'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-4 text-center text-gray-500">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top Clientes -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-green-600">
                        <h3 class="text-lg font-semibold text-white"> Top Clientes</h3>
                    </div>
                    <div class="p-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                    <th class="text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topClientes as $index => $cliente)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 text-sm font-bold text-gray-900">{{ $index + 1 }}</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            {{ $cliente['nombre'] }}
                                            <div class="text-xs text-gray-500">{{ $cliente['compras'] }} compras</div>
                                        </td>
                                        <td class="py-3 text-sm text-right font-semibold text-gray-900">
                                            ${{ number_format($cliente['total'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-4 text-center text-gray-500">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top Productos -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-purple-600">
                        <h3 class="text-lg font-semibold text-white"> Top Productos</h3>
                    </div>
                    <div class="p-6">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                    <th class="text-right text-xs font-medium text-gray-500 uppercase">Vendidos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProductos as $index => $producto)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-3 text-sm font-bold text-gray-900">{{ $index + 1 }}</td>
                                        <td class="py-3 text-sm text-gray-900">
                                            {{ $producto['nombre'] }}
                                            <div class="text-xs text-gray-500">${{ number_format($producto['ingresos'], 2) }}</div>
                                        </td>
                                        <td class="py-3 text-sm text-right font-semibold text-gray-900">
                                            {{ number_format($producto['cantidad'], 0) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="py-4 text-center text-gray-500">Sin datos</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- ========================================
                 ESTADÍSTICAS DE VISITAS (Si existe)
            ======================================== -->
            @if($visitasStats)
                <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4"> Estadísticas de Visitas</h3>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $visitasStats['total'] }}</div>
                            <div class="text-sm text-gray-600">Total Visitas</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $visitasStats['con_venta'] }}</div>
                            <div class="text-sm text-gray-600">Con Venta</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-red-600">{{ $visitasStats['sin_venta'] }}</div>
                            <div class="text-sm text-gray-600">Sin Venta</div>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-purple-600">{{ $visitasStats['tasa_conversion'] }}%</div>
                            <div class="text-sm text-gray-600">Tasa Conversión</div>
                        </div>
                    </div>

                    @if($visitasStats['motivos_no_venta']->count() > 0)
                        <div class="mt-4">
                            <h4 class="font-semibold text-gray-700 mb-2">Motivos de No Venta:</h4>
                            <div class="space-y-2">
                                @foreach($visitasStats['motivos_no_venta'] as $motivo)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-700">{{ $motivo['motivo'] }}</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ $motivo['total'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ✅ NUEVO: Clientes sin venta --}}
                    @if(isset($visitasStats['clientes_sin_venta']) && $visitasStats['clientes_sin_venta']->count() > 0)
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-700 mb-3">Clientes sin venta (Top {{ $visitasStats['clientes_sin_venta']->count() }})</h4>

                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="text-left px-4 py-2 text-xs font-semibold text-gray-600 uppercase">Cliente</th>
                                            <th class="text-center px-4 py-2 text-xs font-semibold text-gray-600 uppercase">Visitas sin venta</th>
                                            <th class="text-right px-4 py-2 text-xs font-semibold text-gray-600 uppercase">Última visita</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visitasStats['clientes_sin_venta'] as $row)
                                            @php
                                                $clienteUrl = \Illuminate\Support\Facades\Route::has('clientes.show')
                                                    ? route('clientes.show', $row['cliente_id'])
                                                    : (\Illuminate\Support\Facades\Route::has('clientes.edit')
                                                        ? route('clientes.edit', $row['cliente_id'])
                                                        : route('clientes.index'));
                                            @endphp
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="px-4 py-2">
                                                    <a href="{{ $clienteUrl }}" class="text-blue-700 hover:underline">
                                                        {{ $row['cliente'] }}
                                                    </a>
                                                </td>
                                                <td class="px-4 py-2 text-center font-semibold text-gray-900">
                                                    {{ $row['total'] }}
                                                </td>
                                                <td class="px-4 py-2 text-right text-gray-700">
                                                    {{ $row['ultima_fecha'] ?? '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    {{-- ✅ NUEVO: Últimas visitas sin venta --}}
                    @if(isset($visitasStats['ultimas_sin_venta']) && $visitasStats['ultimas_sin_venta']->count() > 0)
                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-700 mb-3">Últimas visitas sin venta</h4>

                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="text-left px-4 py-2 text-xs font-semibold text-gray-600 uppercase">Fecha</th>
                                            <th class="text-left px-4 py-2 text-xs font-semibold text-gray-600 uppercase">Cliente</th>
                                            <th class="text-left px-4 py-2 text-xs font-semibold text-gray-600 uppercase">Vendedor</th>
                                            <th class="text-left px-4 py-2 text-xs font-semibold text-gray-600 uppercase">Motivo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($visitasStats['ultimas_sin_venta'] as $r)
                                            <tr class="border-t hover:bg-gray-50">
                                                <td class="px-4 py-2 text-gray-700">{{ $r['fecha'] }} {{ $r['hora'] ? '· '.$r['hora'] : '' }}</td>
                                                <td class="px-4 py-2 text-gray-900">{{ $r['cliente'] }}</td>
                                                <td class="px-4 py-2 text-gray-700">{{ $r['vendedor'] }}</td>
                                                <td class="px-4 py-2 text-gray-700">{{ $r['motivo'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                </div>
            @endif

        </div>
    </div>

    <!-- ========================================
         SCRIPTS PARA GRÁFICAS (Chart.js)
    ======================================== -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Helper: formato con separador de miles
            const formatNumber = (num) => {
                try {
                    return Number(num).toLocaleString('es-MX');
                } catch (e) {
                    return num;
                }
            };

            // Ventas por Día
            const ventasPorDiaCtx = document.getElementById('ventasPorDiaChart')?.getContext('2d');
            if (ventasPorDiaCtx) {
                new Chart(ventasPorDiaCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($ventasPorDia->pluck('fecha')) !!},
                        datasets: [{
                            label: 'Ventas',
                            data: {!! json_encode($ventasPorDia->pluck('total')) !!},
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + formatNumber(value);
                                    }
                                }
                            }
                        },
                        interaction: { mode: 'index', intersect: false }
                    }
                });
            }

            // Ventas por Categoría
            const ventasPorCategoriaCtx = document.getElementById('ventasPorCategoriaChart')?.getContext('2d');
            if (ventasPorCategoriaCtx) {
                new Chart(ventasPorCategoriaCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($ventasPorCategoria->pluck('categoria')) !!},
                        datasets: [{
                            data: {!! json_encode($ventasPorCategoria->pluck('total')) !!},
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(239, 68, 68, 0.8)',
                                'rgba(139, 92, 246, 0.8)',
                                'rgba(236, 72, 153, 0.8)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'right' } }
                    }
                });
            }

            // Inventario por Almacén (Bar) con tooltip separador de miles
            const inventarioPorAlmacenCtx = document.getElementById('inventarioPorAlmacenChart')?.getContext('2d');
            if (inventarioPorAlmacenCtx) {
                new Chart(inventarioPorAlmacenCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($inventarioPorAlmacen->pluck('nombre')) !!},
                        datasets: [{
                            label: 'Unidades',
                            data: {!! json_encode($inventarioPorAlmacen->pluck('total_unidades')) !!},
                            backgroundColor: 'rgba(139, 92, 246, 0.7)',
                            borderColor: 'rgba(139, 92, 246, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        return 'Unidades: ' + formatNumber(value);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return formatNumber(value);
                                    }
                                }
                            }
                        }
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
