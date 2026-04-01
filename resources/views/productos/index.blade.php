<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Catálogo de Productos</h2>
                <p class="text-sm text-gray-500 mt-0.5">Gestiona tu inventario de productos</p>
            </div>
            <a href="{{ route('productos.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar Producto
            </a>
        </div>
    </x-slot>

    <div class="py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">
        {{-- ── PANEL DE FILTROS ── --}}
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('productos.index') }}" class="flex flex-wrap items-end gap-3">

                {{-- Búsqueda --}}
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Buscar</label>
                    <div class="relative">
                        <svg class="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/>
                        </svg>
                        <input type="text" name="buscar" value="{{ request('buscar') }}"
                            placeholder="Nombre o marca..."
                            class="w-full py-2 pr-3 text-sm transition border border-gray-300 rounded-lg outline-none pl-9 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"/>
                    </div>
                </div>

                {{-- Categoría --}}
                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Categoría</label>
                    <select name="categoria_id"
                        class="w-full py-2 pl-3 pr-8 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todas</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Estado --}}
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Estado</label>
                    <select name="estado"
                        class="w-full py-2 pl-3 pr-8 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Todos</option>
                        <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>

                {{-- Botones --}}
                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        Filtrar
                    </button>
                    @if(request()->hasAny(['buscar','categoria_id','estado']))
                        <a href="{{ route('productos.index') }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── STATS ── --}}
        <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
            @php
                $totalActivos   = $productos->total();
                $conMovimientos = $statsConMovimientos ?? 0;
            @endphp
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-indigo-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsTotal }}</div>
                    <div class="text-xs text-gray-500">Total productos</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-green-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsActivos }}</div>
                    <div class="text-xs text-gray-500">Activos</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-gray-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsInactivos }}</div>
                    <div class="text-xs text-gray-500">Inactivos</div>
                </div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-blue-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
                <div>
                    <div class="text-xl font-bold text-gray-900">{{ $statsConMovimientos }}</div>
                    <div class="text-xs text-gray-500">Con movimientos</div>
                </div>
            </div>
        </div>

        {{-- ── TABLA ── --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">

            {{-- Barra superior de resultados --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600">
                    Mostrando <span class="font-semibold text-gray-900">{{ $productos->firstItem() ?? 0 }}</span>
                    – <span class="font-semibold text-gray-900">{{ $productos->lastItem() ?? 0 }}</span>
                    de <span class="font-semibold text-gray-900">{{ $productos->total() }}</span> productos
                </span>
                <span class="text-xs text-gray-400">Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Producto</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Categoría</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Unidad</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-right text-gray-500 uppercase">Precio base</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Estado</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($productos as $producto)
                            <tr class="hover:bg-gray-50 transition-colors {{ !$producto->activo ? 'opacity-60' : '' }}">

                                {{-- Nombre + marca + badges --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-start gap-3">
                                        {{-- Avatar inicial --}}
                                        <div class="flex items-center justify-center text-sm font-bold text-indigo-700 bg-indigo-100 rounded-lg w-9 h-9 shrink-0">
                                            {{ strtoupper(substr($producto->nombre, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $producto->nombre }}</div>
                                            @if($producto->marca)
                                                <div class="text-xs text-gray-500 mt-0.5">{{ $producto->marca }}</div>
                                            @endif
                                            <div class="flex flex-wrap gap-1 mt-1">
                                                @if(!$producto->activo)
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-600">
                                                        Inactivo
                                                    </span>
                                                @endif
                                                @if($producto->tiene_movimientos)
                                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-blue-100 text-blue-700">
                                                        Con movimientos
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Categoría --}}
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                        {{ $producto->categoria->nombre }}
                                    </span>
                                </td>

                                {{-- Unidad --}}
                                <td class="px-5 py-3.5 text-gray-600 text-sm">
                                    {{ $producto->unidadMedida->nombre }}
                                </td>

                                {{-- Precio --}}
                                <td class="px-5 py-3.5 text-right font-semibold text-gray-900">
                                    ${{ number_format($producto->precio, 2) }}
                                </td>

                                {{-- Estado badge --}}
                                <td class="px-5 py-3.5 text-center">
                                    @if($producto->activo)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                            Inactivo
                                        </span>
                                    @endif
                                </td>

                                {{-- Acciones --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">

                                        {{-- Editar --}}
                                        <a href="{{ route('productos.edit', $producto) }}"
                                            title="Editar"
                                            class="inline-flex items-center justify-center w-8 h-8 transition-colors border rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border-amber-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        {{-- Toggle activo/inactivo --}}
                                        <form action="{{ route('productos.toggle', $producto) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" title="{{ $producto->activo ? 'Inactivar' : 'Activar' }}"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors border
                                                {{ $producto->activo
                                                    ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100 border-yellow-200'
                                                    : 'bg-green-50 text-green-600 hover:bg-green-100 border-green-200' }}">
                                                @if($producto->activo)
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>

                                        {{-- Eliminar --}}
                                        @if($producto->puede_eliminar)
                                            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar {{ addslashes($producto->nombre) }} definitivamente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Eliminar"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 transition-colors border border-red-200 rounded-lg bg-red-50 hover:bg-red-100">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" title="No se puede eliminar: tiene movimientos"
                                                class="inline-flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                        </svg>
                                        <div class="font-medium text-gray-500">Sin productos</div>
                                        <div class="text-sm text-gray-400">No se encontraron productos con los filtros aplicados.</div>
                                        <a href="{{ route('productos.index') }}" class="text-sm text-indigo-600 hover:underline">Limpiar filtros</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ── PAGINADO ── --}}
            @if($productos->hasPages())
                <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100 bg-gray-50">
                    <div class="text-sm text-gray-600">
                        {{ $productos->total() }} resultados
                    </div>
                    <div class="flex items-center gap-1">
                        {{-- Anterior --}}
                        @if($productos->onFirstPage())
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $productos->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>
                        @endif

                        {{-- Números de página --}}
                        @foreach($productos->getUrlRange(max(1, $productos->currentPage()-2), min($productos->lastPage(), $productos->currentPage()+2)) as $page => $url)
                            @if($page == $productos->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 text-sm font-semibold text-white bg-indigo-600 rounded-lg">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach

                        {{-- Siguiente --}}
                        @if($productos->hasMorePages())
                            <a href="{{ $productos->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}"
                                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-all border border-transparent rounded-lg hover:bg-white hover:shadow-sm hover:border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <span class="inline-flex items-center justify-center w-8 h-8 text-gray-300 rounded-lg cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>