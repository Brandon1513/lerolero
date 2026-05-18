<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Cierres de Ruta</h2>
                <p class="text-sm text-gray-500 mt-0.5">Revisión y cuadre de cierres de vendedores</p>
            </div>
        </div>
    </x-slot>
    <div class="py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('cierres.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Vendedor</label>
                    <select name="vendedor_id" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        @foreach($vendedores as $vendedor)<option value="{{ $vendedor->id }}" {{ (string)request('vendedor_id')===(string)$vendedor->id?'selected':'' }}>{{ $vendedor->name }}</option>@endforeach
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Estatus</label>
                    <select name="estatus" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estatus')==='pendiente'?'selected':'' }}>Pendiente</option>
                        <option value="cuadrado" {{ request('estatus')==='cuadrado'?'selected':'' }}>Cuadrado</option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Cerrado por</label>
                    <select name="cerrado_por" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        @foreach($admins as $admin)<option value="{{ $admin->id }}" {{ (string)request('cerrado_por')===(string)$admin->id?'selected':'' }}>{{ $admin->name }}</option>@endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">Filtrar</button>
                    <a href="{{ route('cierres.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">Limpiar</a>
                </div>
            </form>
        </div>

        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">
                    @if($cierres->count() > 0)
                        <span class="font-semibold text-gray-900">{{ $cierres->count() }}</span> cierre{{ $cierres->count()>1?'s':'' }}
                    @else
                        <span class="text-red-600">No se encontraron cierres con los filtros aplicados.</span>
                    @endif
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-4 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Vendedor</th>
                            <th class="px-4 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Total Ventas</th>
                            <th class="px-4 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Cobrado hoy</th>
                            <th class="px-4 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Crédito (día)</th>
                            <th class="px-4 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Estatus</th>
                            <th class="px-4 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Cerrado Por</th>
                            <th class="px-4 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($cierres as $cierre)
                            @php
                                $r = $resumenIndex[$cierre->id] ?? [];
                                $cobradoTotal = (float)($r['cobrado_hoy_total'] ?? 0);
                                $cobradoDia   = (float)($r['cobrado_hoy_ventas_dia'] ?? 0);
                                $cobradoAnt   = (float)($r['cobrado_hoy_saldos_anteriores'] ?? 0);
                                $creditoDia   = (float)($r['credito_dia_total'] ?? 0);
                                $metodos = $r['metodos'] ?? [];
                                $mEfe = (float)($metodos['efectivo'] ?? 0);
                                $mTra = (float)($metodos['transferencia'] ?? 0);
                                $mTar = (float)($metodos['tarjeta'] ?? 0);
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3.5 text-center">
                                    <span class="font-mono text-xs font-semibold text-gray-600">#{{ $cierre->id }}</span>
                                    @if($cierre->cierre_anterior_id)
                                        <div class="mt-1"><span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold text-amber-800 bg-amber-100 rounded-full">↺ Reapertura</span></div>
                                        <div class="text-[10px] text-gray-400 mt-0.5">ant: #{{ $cierre->cierre_anterior_id }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 font-medium text-gray-900">{{ $cierre->vendedor->name }}</td>
                                <td class="px-4 py-3.5 text-gray-700">{{ \Carbon\Carbon::parse($cierre->fecha)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3.5 text-right font-semibold text-gray-900">${{ number_format($cierre->total_ventas, 2) }}</td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="font-semibold text-emerald-700">${{ number_format($cobradoTotal, 2) }}</div>
                                    <div class="mt-1 text-xs text-gray-500">Día: <span class="font-medium text-gray-700">${{ number_format($cobradoDia, 2) }}</span> · Ant: <span class="font-medium text-indigo-700">${{ number_format($cobradoAnt, 2) }}</span></div>
                                    @if(($mEfe + $mTra + $mTar) > 0.01)
                                        <div class="flex flex-wrap justify-end gap-1 mt-1">
                                            @if($mEfe>0.01)<span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium text-gray-800 bg-gray-100 border rounded-full">💵 ${{ number_format($mEfe,0) }}</span>@endif
                                            @if($mTra>0.01)<span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium text-gray-800 bg-gray-100 border rounded-full">🔁 ${{ number_format($mTra,0) }}</span>@endif
                                            @if($mTar>0.01)<span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-medium text-gray-800 bg-gray-100 border rounded-full">💳 ${{ number_format($mTar,0) }}</span>@endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="font-semibold {{ $creditoDia > 0.01 ? 'text-red-700' : 'text-gray-700' }}">${{ number_format($creditoDia, 2) }}</div>
                                    <div class="mt-1">
                                        @if($creditoDia > 0.01)<span class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-red-800 bg-red-100 rounded-full">A crédito</span>
                                        @else<span class="inline-flex items-center px-2 py-0.5 text-xs font-medium text-emerald-800 bg-emerald-100 rounded-full">Pagado</span>@endif
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 capitalize">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full {{ $cierre->estatus==='cuadrado'?'bg-green-100 text-green-800':'bg-amber-100 text-amber-800' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $cierre->estatus==='cuadrado'?'bg-green-500':'bg-amber-500' }}"></span>
                                        {{ ucfirst($cierre->estatus) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-gray-600">{{ $cierre->cerradoPor?->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('cierres.show', $cierre) }}"
                                            class="inline-flex items-center justify-center w-8 h-8 text-blue-600 transition-colors border border-blue-200 rounded-lg bg-blue-50 hover:bg-blue-100" title="Ver detalle">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        @can('cierres.liberar')
                                        @if($cierre->estatus === 'pendiente')
                                            <form method="POST" action="{{ route('cierres.liberar', $cierre) }}"
                                                onsubmit="return confirm('¿Liberar al vendedor para que pueda vender de nuevo?')">
                                                @csrf
                                                <button type="submit" title="Liberar"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-amber-700 transition-colors border border-amber-300 rounded-lg bg-amber-50 hover:bg-amber-100">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($cierres->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">{{ $cierres->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
    @if(session('toast'))
        <div id="toast" class="fixed bottom-4 right-4 z-50 p-4 rounded shadow-md text-white {{ session('toast')==='cuadrado'?'bg-green-600':'bg-yellow-500' }}">
            @switch(session('toast'))
                @case('cuadrado') ✅ Efectivo cuadrado correctamente. @break
                @case('faltan') ⚠️ Faltan pesos en el efectivo entregado. @break
                @case('sobran') ⚠️ Sobraron pesos en el efectivo entregado. @break
            @endswitch
        </div>
        <script>setTimeout(()=>{const t=document.getElementById('toast');if(t)t.style.opacity='0'},3500)</script>
    @endif
</x-app-layout>