<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Cierre de Ruta - {{ $cierre->vendedor->name }}
                ({{ \Carbon\Carbon::parse($cierre->fecha)->format('d/m/Y') }})
            </h2>

            <a href="{{ route('cierres.index') }}" class="px-4 py-2 text-sm bg-gray-100 rounded hover:bg-gray-200">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="py-12 mx-auto space-y-8 max-w-7xl sm:px-6 lg:px-8">

        {{-- ====================== RESUMEN GENERAL ====================== --}}
        <div class="p-6 space-y-3 bg-white rounded-lg shadow">
            <h3 class="text-lg font-bold text-gray-700">Resumen de la Ruta</h3>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="p-4 border rounded-lg">
                    <div class="text-sm text-gray-500">Total ventas (día)</div>
                    <div class="text-2xl font-bold text-gray-900">
                        ${{ number_format($resumen['ventas_dia']['total_ventas'], 2) }}
                    </div>
                </div>

                <div class="p-4 border rounded-lg">
                    <div class="text-sm text-gray-500">Cobrado hoy (TOTAL)</div>
                    <div class="text-2xl font-bold text-emerald-700">
                        ${{ number_format($resumen['cobros_hoy']['total'], 2) }}
                    </div>
                    <div class="mt-1 text-xs text-gray-500">
                        Incluye cobros de ventas anteriores.
                    </div>
                </div>

                <div class="p-4 border rounded-lg">
                    <div class="text-sm text-gray-500">Estatus</div>
                    <div class="text-2xl font-bold text-gray-900 capitalize">
                        {{ $cierre->estatus }}
                    </div>
                    @if ($cierre->cerradoPor)
                        <div class="mt-1 text-xs text-gray-500">
                            Cerrado por: <span class="font-medium">{{ $cierre->cerradoPor->name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ✅ Mini-card PREMIUM: resumen 2 segundos + chips por método --}}
@php
    $cobroDia      = (float) ($resumen['cobros_hoy']['ventas_dia'] ?? 0);
    $cobroAnterior = (float) ($resumen['cobros_hoy']['ventas_anteriores'] ?? 0);

    $md = $resumen['cobros_hoy']['metodos_ventas_dia'] ?? [];
    $ma = $resumen['cobros_hoy']['metodos_ventas_anteriores'] ?? [];

    $diaEfe = (float) ($md['efectivo'] ?? 0);
    $diaTra = (float) ($md['transferencia'] ?? 0);
    $diaTar = (float) ($md['tarjeta'] ?? 0);

    $antEfe = (float) ($ma['efectivo'] ?? 0);
    $antTra = (float) ($ma['transferencia'] ?? 0);
    $antTar = (float) ($ma['tarjeta'] ?? 0);
@endphp

<div class="mt-2">
    @if(($cobroDia + $cobroAnterior) > 0.01)
        <div class="flex items-start gap-3 p-4 border rounded-lg
            {{ $cobroAnterior > 0.01 ? 'bg-indigo-50 text-indigo-800 border-indigo-200' : 'bg-gray-50 text-gray-700 border-gray-200' }}">
            <div class="mt-0.5">
                {{ $cobroAnterior > 0.01 ? '💡' : '✅' }}
            </div>

            <div class="w-full text-sm">
                <div class="font-semibold">
                    @if($cobroAnterior > 0.01)
                        Hoy se cobraron ${{ number_format($cobroAnterior, 2) }} de saldos anteriores.
                    @else
                        Hoy no se cobró nada de saldos anteriores.
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-3 mt-2 sm:grid-cols-2">
                    {{-- Ventas del día --}}
                    <div class="p-3 bg-white border rounded">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-600">Cobrado hoy de ventas del día:</span>
                            <span class="font-semibold text-gray-900">${{ number_format($cobroDia, 2) }}</span>
                        </div>

                        {{-- Chips por método (ventas del día) --}}
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="px-2 py-1 text-xs border rounded-full bg-gray-50">
                                💵 Efectivo: <span class="font-semibold">${{ number_format($diaEfe, 2) }}</span>
                            </span>
                            <span class="px-2 py-1 text-xs border rounded-full bg-gray-50">
                                🏦 Transfer: <span class="font-semibold">${{ number_format($diaTra, 2) }}</span>
                            </span>
                            <span class="px-2 py-1 text-xs border rounded-full bg-gray-50">
                                💳 Tarjeta: <span class="font-semibold">${{ number_format($diaTar, 2) }}</span>
                            </span>
                        </div>
                    </div>

                    {{-- Saldos anteriores --}}
                    <div class="p-3 bg-white border rounded">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-600">Cobrado hoy de saldos anteriores:</span>
                            <span class="font-semibold {{ $cobroAnterior > 0.01 ? 'text-indigo-700' : 'text-gray-900' }}">
                                ${{ number_format($cobroAnterior, 2) }}
                            </span>
                        </div>

                        {{-- Chips por método (saldos anteriores) --}}
                        <div class="flex flex-wrap gap-2 mt-2">
                            <span class="px-2 py-1 text-xs border rounded-full bg-gray-50">
                                💵 Efectivo: <span class="font-semibold">${{ number_format($antEfe, 2) }}</span>
                            </span>
                            <span class="px-2 py-1 text-xs border rounded-full bg-gray-50">
                                🏦 Transfer: <span class="font-semibold">${{ number_format($antTra, 2) }}</span>
                            </span>
                            <span class="px-2 py-1 text-xs border rounded-full bg-gray-50">
                                💳 Tarjeta: <span class="font-semibold">${{ number_format($antTar, 2) }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-2 text-xs {{ $cobroAnterior > 0.01 ? 'text-indigo-700' : 'text-gray-500' }}">
                    *Así se entiende rápido: cuánto fue del día vs cuánto fue de cobranza de saldos anteriores (y por método).
                </div>
            </div>
        </div>
    @else
        <div class="flex items-start gap-3 p-4 text-gray-700 border rounded-lg bg-gray-50">
            <div class="mt-0.5">✅</div>
            <div class="text-sm">
                <div class="font-semibold">Hoy no hubo cobros registrados.</div>
                <div class="mt-1 text-xs text-gray-500">
                    *No hay cobros de ventas del día ni de saldos anteriores.
                </div>
            </div>
        </div>
    @endif
</div>


            {{-- Traslado --}}
            <div class="pt-2">
                @if ($cierre->traslado_id)
                    <a href="{{ route('traslados.show', $cierre->traslado_id) }}"
                       class="inline-flex items-center px-4 py-2 text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                        Ver Traslado Generado
                    </a>
                @else
                    <div class="inline-block px-4 py-2 text-sm text-yellow-800 bg-yellow-100 rounded-md">
                        No se ha registrado un traslado para este cierre.
                    </div>
                @endif
            </div>

            {{-- ✅ Cuadre (SOLO si ya está cerrado) --}}
            @if ($cierre->estatus == 'cuadrado' && !is_null($cierre->total_efectivo))
                @php
                    $efectivoEsperado = (float) ($resumen['cobros_hoy']['metodos']['efectivo'] ?? 0);
                    $diferencia = (float)$cierre->total_efectivo - $efectivoEsperado;
                @endphp

                <div class="mt-4 p-4 rounded-lg
                    {{ abs($diferencia) <= 0.01 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    <div class="mb-1 font-semibold">Cuadre de efectivo</div>

                    <div class="text-sm">
                        <div class="flex justify-between">
                            <span>Efectivo esperado (cobrado hoy):</span>
                            <span class="font-semibold">${{ number_format($efectivoEsperado, 2) }}</span>
                        </div>
                        <div class="flex justify-between mt-1">
                            <span>Efectivo entregado:</span>
                            <span class="font-semibold">${{ number_format($cierre->total_efectivo, 2) }}</span>
                        </div>

                        <div class="mt-2 font-semibold">
                            @if (abs($diferencia) <= 0.01)
                                ✅ Efectivo cuadrado correctamente.
                            @elseif ($diferencia < 0)
                                ⚠️ Faltan ${{ number_format(abs($diferencia), 2) }} para cuadrar el efectivo.
                            @else
                                ⚠️ Sobraron ${{ number_format($diferencia, 2) }} en el efectivo entregado.
                            @endif
                        </div>

                        <div class="mt-1 text-xs text-gray-600">
                            *El cuadre se hace contra el <strong>efectivo cobrado hoy</strong>, no contra el total de ventas.
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ====================== RESUMEN VENTAS / COBROS ====================== --}}
        <div class="p-6 bg-white rounded-lg shadow">
            <h3 class="mb-4 text-lg font-bold text-gray-700">Resumen de Ventas / Cobros</h3>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div class="p-4 border rounded-lg">
                    <div class="text-sm text-gray-500">Ventas (día)</div>
                    <div class="mt-1 text-2xl font-semibold">
                        ${{ number_format($resumen['ventas_dia']['total_ventas'], 2) }}
                    </div>
                </div>

                <div class="p-4 border rounded-lg">
                    <div class="text-sm text-gray-500">Cobrado hoy (TOTAL)</div>
                    <div class="mt-1 text-2xl font-semibold text-emerald-700">
                        ${{ number_format($resumen['cobros_hoy']['total'], 2) }}
                    </div>
                </div>

                <div class="p-4 border rounded-lg">
                    <div class="text-sm text-gray-500">Cobrado hoy (ventas del día)</div>
                    <div class="mt-1 text-2xl font-semibold text-gray-900">
                        ${{ number_format($resumen['cobros_hoy']['ventas_dia'], 2) }}
                    </div>
                </div>

                <div class="p-4 border rounded-lg">
                    <div class="text-sm text-gray-500">Cobrado hoy (saldos anteriores)</div>
                    <div class="mt-1 text-2xl font-semibold text-indigo-700">
                        ${{ number_format($resumen['cobros_hoy']['ventas_anteriores'], 2) }}
                    </div>
                </div>
            </div>

            {{-- Métodos --}}
            <div class="grid grid-cols-1 gap-4 mt-6 md:grid-cols-3">
                @php
                    $m = $resumen['cobros_hoy']['metodos'] ?? [];
                    $md = $resumen['cobros_hoy']['metodos_ventas_dia'] ?? [];
                    $ma = $resumen['cobros_hoy']['metodos_ventas_anteriores'] ?? [];
                @endphp

                <div class="p-4 border rounded-lg">
                    <div class="mb-2 text-sm font-semibold text-gray-700">Métodos (cobrado hoy)</div>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between"><span>Efectivo:</span><span class="font-semibold">${{ number_format($m['efectivo'] ?? 0, 2) }}</span></div>
                        <div class="flex justify-between"><span>Transferencia:</span><span class="font-semibold">${{ number_format($m['transferencia'] ?? 0, 2) }}</span></div>
                        <div class="flex justify-between"><span>Tarjeta:</span><span class="font-semibold">${{ number_format($m['tarjeta'] ?? 0, 2) }}</span></div>
                    </div>
                </div>

                <div class="p-4 border rounded-lg">
                    <div class="mb-2 text-sm font-semibold text-gray-700">Métodos (ventas del día)</div>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between"><span>Efectivo:</span><span class="font-semibold">${{ number_format($md['efectivo'] ?? 0, 2) }}</span></div>
                        <div class="flex justify-between"><span>Transferencia:</span><span class="font-semibold">${{ number_format($md['transferencia'] ?? 0, 2) }}</span></div>
                        <div class="flex justify-between"><span>Tarjeta:</span><span class="font-semibold">${{ number_format($md['tarjeta'] ?? 0, 2) }}</span></div>
                    </div>
                </div>

                <div class="p-4 border rounded-lg">
                    <div class="mb-2 text-sm font-semibold text-gray-700">Métodos (saldos anteriores)</div>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between"><span>Efectivo:</span><span class="font-semibold">${{ number_format($ma['efectivo'] ?? 0, 2) }}</span></div>
                        <div class="flex justify-between"><span>Transferencia:</span><span class="font-semibold">${{ number_format($ma['transferencia'] ?? 0, 2) }}</span></div>
                        <div class="flex justify-between"><span>Tarjeta:</span><span class="font-semibold">${{ number_format($ma['tarjeta'] ?? 0, 2) }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Cobranza de saldos anteriores (por cliente) --}}
            <div class="p-4 mt-6 border rounded-lg">
                <h4 class="font-semibold text-gray-700">Cobranza de saldos anteriores (hoy)</h4>

                @if(($resumen['cobros_hoy']['ventas_anteriores'] ?? 0) <= 0.01)
                    <p class="mt-2 text-sm text-gray-500">No hubo cobranza de saldos anteriores en esta ruta.</p>
                @else
                    <div class="mt-3 overflow-x-auto">
                        <table class="w-full text-sm border border-collapse border-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left border">Cliente</th>
                                    <th class="px-3 py-2 text-center border"># Ventas involucradas</th>
                                    <th class="px-3 py-2 text-right border">Cobrado hoy</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cobranzaAnteriorPorCliente as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 border">{{ $row['cliente'] }}</td>
                                        <td class="px-3 py-2 text-center border">{{ $row['ventas_involucradas'] }}</td>
                                        <td class="px-3 py-2 font-semibold text-right text-indigo-700 border">
                                            ${{ number_format($row['monto'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Pendientes del día --}}
            @if(isset($clientesPendientesDia) && $clientesPendientesDia->count() > 0)
                <div class="mt-6">
                    <h4 class="mb-2 font-semibold text-gray-700">Clientes con saldo pendiente (del día)</h4>

                    <div class="overflow-x-auto border rounded-lg">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-xs font-semibold text-left text-gray-600 uppercase">Cliente</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-center text-gray-600 uppercase"># Ventas</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-right text-gray-600 uppercase">Pendiente</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clientesPendientesDia as $row)
                                    <tr class="border-t hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-900">{{ $row['cliente'] }}</td>
                                        <td class="px-4 py-2 text-center text-gray-700">{{ $row['ventas'] }}</td>
                                        <td class="px-4 py-2 font-semibold text-right text-red-700">
                                            ${{ number_format($row['pendiente'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Detalle de pagos cobrados hoy (toggle) --}}
            <div class="p-4 mt-6 bg-white border rounded-lg" x-data="{ open:false }">
                <div class="flex items-center justify-between">
                    <h4 class="font-semibold">Detalle de pagos cobrados hoy</h4>

                    <button type="button"
                            @click="open = !open"
                            class="px-3 py-2 text-sm font-medium text-white bg-gray-800 rounded hover:bg-gray-900">
                        <span x-text="open ? 'Ocultar' : 'Mostrar'"></span>
                    </button>
                </div>

                <div x-show="open" x-cloak class="mt-4">
                    @if(($pagosHoyDetalle ?? collect())->count() === 0)
                        <p class="text-sm text-gray-500">No hay pagos registrados hoy.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm border border-collapse border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left border">Cliente</th>
                                        <th class="px-3 py-2 text-center border">Venta</th>
                                        <th class="px-3 py-2 text-center border">Fecha venta</th>
                                        <th class="px-3 py-2 text-center border">Cobrado</th>
                                        <th class="px-3 py-2 text-center border">Método</th>
                                        <th class="px-3 py-2 text-right border">Monto</th>
                                        <th class="px-3 py-2 text-left border">Referencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pagosHoyDetalle as $p)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2 border">{{ $p['cliente'] }}</td>
                                            <td class="px-3 py-2 text-center border">#{{ $p['venta_id'] }}</td>
                                            <td class="px-3 py-2 text-center border">{{ $p['fecha_venta'] }}</td>
                                            <td class="px-3 py-2 text-center border">{{ $p['fecha_cobro'] }}</td>
                                            <td class="px-3 py-2 text-center capitalize border">{{ $p['metodo'] }}</td>
                                            <td class="px-3 py-2 font-semibold text-right border">
                                                ${{ number_format($p['monto'], 2) }}
                                            </td>
                                            <td class="px-3 py-2 border">{{ $p['referencia'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="mt-3 text-xs text-gray-500">
                        *Aquí se ven todos los cobros del día (ventas del día + abonos de días anteriores). Ideal para auditoría.
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================== INVENTARIOS ====================== --}}
        <div class="p-6 bg-white rounded-lg shadow">
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

        <div class="p-6 bg-white rounded-lg shadow">
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

        {{-- Cambios --}}
        @if($cierre->cambios)
            <div class="p-6 bg-white rounded-lg shadow">
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

        {{-- ====================== FORM CIERRE ====================== --}}
        @if ($cierre->estatus == 'pendiente')
            @php
                $efectivoEsperadoHoy = (float) ($resumen['cobros_hoy']['metodos']['efectivo'] ?? 0);
            @endphp

            <div class="p-6 space-y-4 bg-white rounded-lg shadow">
                <h3 class="text-lg font-bold text-gray-700">Finalizar Cierre de Ruta</h3>

                <div class="p-4 text-sm text-blue-800 rounded bg-blue-50">
                    <strong>Efectivo esperado (cobrado hoy):</strong>
                    ${{ number_format($efectivoEsperadoHoy, 2) }}
                    <span class="block mt-1 text-xs text-blue-700">
                        *Este es el monto recomendado para cuadrar el efectivo.
                    </span>
                </div>

                <form method="POST" action="{{ route('cierres.update', $cierre) }}" onsubmit="return validarEfectivo()">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="total_efectivo" class="block mb-2 text-sm font-medium text-gray-700">
                            Total Efectivo Entregado
                        </label>
                        <input type="number" step="0.01" name="total_efectivo" id="total_efectivo"
                               class="w-full p-2 border rounded" required>
                    </div>

                    <div>
                        <label for="observaciones" class="block mb-2 text-sm font-medium text-gray-700">Observaciones</label>
                        <textarea name="observaciones" id="observaciones" rows="4" class="w-full p-2 border rounded"></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2 text-white transition-all bg-green-500 rounded-md hover:bg-green-600">
                            Cerrar Ruta
                        </button>
                    </div>
                </form>
            </div>

            <script>
                function validarEfectivo() {
                    const efectivoEsperado = {{ $efectivoEsperadoHoy }};
                    const efectivo = parseFloat(document.getElementById('total_efectivo').value || '0');

                    const eps = 0.01;

                    if (efectivo + eps < efectivoEsperado) {
                        return confirm(
                            '⚠️ El efectivo entregado es menor al efectivo esperado (cobrado hoy).\n' +
                            'Esperado: $' + efectivoEsperado.toFixed(2) + '\n' +
                            'Entregado: $' + efectivo.toFixed(2) + '\n\n' +
                            '¿Seguro que quieres continuar?'
                        );
                    }

                    return true;
                }
            </script>
        @else
            <div class="p-6 text-center bg-green-100 rounded-lg shadow">
                <p class="font-bold text-green-700">Esta ruta ya fue cerrada.</p>
            </div>
        @endif

    </div>
</x-app-layout>
