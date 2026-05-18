<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Promociones de Venta</h2>
                <p class="text-sm text-gray-500 mt-0.5">Gestiona las promociones activas e inactivas</p>
            </div>
            @can('promociones.crear')
            <a href="{{ route('promociones.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva Promoción
            </a>
            @endcan
        </div>
    </x-slot>
    <div class="py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('promociones.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Buscar</label>
                    <div class="relative">
                        <svg class="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre..." class="w-full py-2 pr-3 text-sm transition border border-gray-300 rounded-lg outline-none pl-9 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
                    </div>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Estado</label>
                    <select name="estado" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('estado')==='activo'?'selected':'' }}>Activas</option>
                        <option value="inactivo" {{ request('estado')==='inactivo'?'selected':'' }}>Inactivas</option>
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Vigencia</label>
                    <select name="vigencia" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todas</option>
                        <option value="vigente" {{ request('vigencia')==='vigente'?'selected':'' }}>Vigentes</option>
                        <option value="proxima" {{ request('vigencia')==='proxima'?'selected':'' }}>Próximas</option>
                        <option value="expirada" {{ request('vigencia')==='expirada'?'selected':'' }}>Expiradas</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">Filtrar</button>
                    @if(request()->hasAny(['buscar','estado','vigencia']))
                        <a href="{{ route('promociones.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">Limpiar</a>
                    @endif
                </div>
            </form>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl"><div><div class="text-xl font-bold text-gray-900">{{ $statsTotal }}</div><div class="text-xs text-gray-500">Total</div></div></div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl"><div><div class="text-xl font-bold text-gray-900">{{ $statsVigentes }}</div><div class="text-xs text-gray-500">Vigentes</div></div></div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl"><div><div class="text-xl font-bold text-gray-900">{{ $statsProximas }}</div><div class="text-xs text-gray-500">Próximas</div></div></div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl"><div><div class="text-xl font-bold text-gray-900">{{ $statsExpiradas }}</div><div class="text-xs text-gray-500">Expiradas</div></div></div>
        </div>
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">Mostrando <span class="font-semibold">{{ $promociones->firstItem() ?? 0 }}</span> – <span class="font-semibold">{{ $promociones->lastItem() ?? 0 }}</span> de <span class="font-semibold">{{ $promociones->total() }}</span> promociones</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Promoción</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Precio</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Productos</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Vigencia</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Estado</th>
                            @canany(['promociones.editar','promociones.eliminar'])
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                            @endcanany
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($promociones as $promo)
                            @php
                                $tieneVentas = \Illuminate\Support\Facades\DB::table('venta_promociones')->where('promocion_id', $promo->id)->exists();
                                $vig = $promo->vigencia_estado;
                                $vigColors = ['vigente'=>'bg-green-100 text-green-700','proxima'=>'bg-blue-100 text-blue-700','expirada'=>'bg-red-100 text-red-700'];
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ !$promo->activo ? 'opacity-60' : '' }}">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-start gap-3">
                                        <div class="flex items-center justify-center text-sm font-bold text-indigo-700 bg-indigo-100 rounded-lg w-9 h-9 shrink-0">{{ strtoupper(substr($promo->nombre, 0, 1)) }}</div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $promo->nombre }}</div>
                                            @if($promo->descripcion)<div class="text-xs text-gray-500 mt-0.5 max-w-[200px] truncate">{{ $promo->descripcion }}</div>@endif
                                            @if($tieneVentas)<span class="inline-flex items-center mt-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Usada en ventas</span>@endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-right"><span class="font-bold text-gray-900">${{ number_format($promo->precio, 2) }}</span></td>
                                <td class="px-5 py-3.5">
                                    <div class="flex flex-col gap-1">
                                        @forelse($promo->productos as $producto)
                                            <div class="flex items-center gap-1.5">
                                                <span class="flex items-center justify-center w-5 h-5 text-xs font-bold text-gray-600 bg-gray-100 rounded shrink-0">{{ strtoupper(substr($producto->nombre, 0, 1)) }}</span>
                                                <span class="text-xs text-gray-700">{{ $producto->nombre }}</span>
                                                <span class="text-xs font-semibold text-gray-400">×{{ $producto->pivot->cantidad }}</span>
                                            </div>
                                        @empty<span class="text-xs text-gray-400">Sin productos</span>@endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="mb-1 text-xs text-gray-600">{{ $promo->fecha_inicio?->format('d/m/Y') ?? '—' }} → {{ $promo->fecha_fin?->format('d/m/Y') ?? '—' }}</div>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full {{ $vigColors[$vig] ?? 'bg-gray-100 text-gray-600' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $vig==='vigente'?'bg-green-500':($vig==='proxima'?'bg-blue-500':'bg-red-400') }}"></span>
                                        {{ $promo->vigencia_label }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($promo->activo)<span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activa</span>
                                    @else<span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactiva</span>@endif
                                </td>
                                @canany(['promociones.editar','promociones.eliminar'])
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @can('promociones.editar')
                                        <a href="{{ route('promociones.edit', $promo) }}" title="Editar" class="inline-flex items-center justify-center w-8 h-8 transition-colors border rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border-amber-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <form action="{{ route('promociones.toggle', $promo) }}" method="POST" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" title="{{ $promo->activo ? 'Inactivar' : 'Activar' }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors border {{ $promo->activo ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100 border-yellow-200' : 'bg-green-50 text-green-600 hover:bg-green-100 border-green-200' }}">
                                                @if($promo->activo)<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                                                @else<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>@endif
                                            </button>
                                        </form>
                                        @endcan
                                        @can('promociones.eliminar')
                                        @if(!$tieneVentas)
                                            <form action="{{ route('promociones.destroy', $promo) }}" method="POST" onsubmit="return confirm('¿Eliminar la promoción {{ addslashes($promo->nombre) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Eliminar" class="inline-flex items-center justify-center w-8 h-8 text-red-600 transition-colors border border-red-200 rounded-lg bg-red-50 hover:bg-red-100">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" title="No se puede eliminar: ya fue usada en ventas" class="inline-flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                                @endcanany
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-16 text-center"><div class="font-medium text-gray-500">Sin promociones</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($promociones->hasPages())
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">{{ $promociones->withQueryString()->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>