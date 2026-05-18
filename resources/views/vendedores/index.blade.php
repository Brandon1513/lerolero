<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Vendedores</h2>
                <p class="text-sm text-gray-500 mt-0.5">Gestión de usuarios y roles del sistema</p>
            </div>
            <a href="{{ route('vendedores.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Agregar Usuario
            </a>
        </div>
    </x-slot>

    <div class="py-8 mx-auto max-w-7xl sm:px-6 lg:px-8">

        {{-- Filtros --}}
        <div class="p-4 mb-4 bg-white border border-gray-200 shadow-sm rounded-xl">
            <form method="GET" action="{{ route('vendedores.index') }}" class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Buscar</label>
                    <div class="relative">
                        <svg class="absolute w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 105 11a6 6 0 0012 0z"/></svg>
                        <input type="text" name="nombre" value="{{ request('nombre') }}" placeholder="Nombre del usuario..."
                            class="w-full py-2 pr-3 text-sm transition border border-gray-300 rounded-lg outline-none pl-9 focus:ring-2 focus:ring-indigo-500"/>
                    </div>
                </div>
                <div class="min-w-[160px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Rol</label>
                    <select name="rol" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="vendedor"         {{ request('rol') === 'vendedor'         ? 'selected' : '' }}>Vendedor</option>
                        <option value="administrador"    {{ request('rol') === 'administrador'    ? 'selected' : '' }}>Administrador</option>
                        <option value="empleado_interno" {{ request('rol') === 'empleado_interno' ? 'selected' : '' }}>Empleado Interno</option>
                    </select>
                </div>
                <div class="min-w-[130px]">
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wide">Estado</label>
                    <select name="estado" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todos</option>
                        <option value="activo"   {{ request('estado') === 'activo'   ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Filtrar
                    </button>
                    @if(request()->hasAny(['nombre','rol','estado']))
                        <a href="{{ route('vendedores.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Stats --}}
        @php
            $total     = $vendedores->count();
            $activos   = $vendedores->where('activo', true)->count();
            $admins    = $vendedores->filter(fn($v) => $v->hasRole('administrador'))->count();
            $inactivos = $vendedores->where('activo', false)->count();
        @endphp
        <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-4">
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-indigo-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $total }}</div><div class="text-xs text-gray-500">Total</div></div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-green-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $activos }}</div><div class="text-xs text-gray-500">Activos</div></div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-purple-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $admins }}</div><div class="text-xs text-gray-500">Admins</div></div>
            </div>
            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center justify-center bg-gray-100 rounded-lg w-9 h-9 shrink-0">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                </div>
                <div><div class="text-xl font-bold text-gray-900">{{ $inactivos }}</div><div class="text-xs text-gray-500">Inactivos</div></div>
            </div>
        </div>

        {{-- Tabla --}}
        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <span class="text-sm text-gray-600"><span class="font-semibold text-gray-900">{{ $total }}</span> usuarios</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Usuario</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Correo</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Registro</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Roles</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Estado</th>
                            <th class="px-5 py-3 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($vendedores as $vendedor)
                            @php
                                $esYo    = auth()->id() === $vendedor->id;
                                $esAdmin = $vendedor->hasRole('administrador');
                                $query   = request()->except('page');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors {{ !$vendedor->activo ? 'opacity-60' : '' }}">

                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                                            {{ $esAdmin ? 'bg-purple-100 text-purple-700' : 'bg-indigo-100 text-indigo-700' }}">
                                            {{ strtoupper(substr($vendedor->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900 flex items-center gap-1.5">
                                                {{ $vendedor->name }}
                                                @if($esYo)
                                                    <span class="text-xs px-1.5 py-0.5 bg-indigo-100 text-indigo-700 rounded font-semibold">Tú</span>
                                                @endif
                                            </div>
                                            @if($vendedor->tiene_movimientos ?? false)
                                                <div class="text-xs text-amber-600 flex items-center gap-1 mt-0.5">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                                    Tiene movimientos
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-3.5 text-sm text-gray-600">{{ $vendedor->email }}</td>

                                <td class="px-5 py-3.5 text-sm text-gray-500">
                                    {{ optional($vendedor->created_at)->format('d/m/Y') }}
                                </td>

                                <td class="px-5 py-3.5">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($vendedor->getRoleNames() as $rol)
                                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full
                                                {{ $rol === 'administrador' ? 'bg-purple-100 text-purple-800' :
                                                   ($rol === 'empleado_interno' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $rol)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <td class="px-5 py-3.5 text-center">
                                    @if($vendedor->activo)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-center gap-1.5">

                                        {{-- Editar --}}
                                        <a href="{{ route('vendedores.edit', $vendedor) }}" title="Editar"
                                            class="inline-flex items-center justify-center w-8 h-8 transition-colors border rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 border-amber-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>

                                        {{-- 🔐 Permisos --}}
                                        <a href="{{ route('vendedores.permisos.edit', $vendedor) }}" title="Gestionar permisos"
                                            class="inline-flex items-center justify-center w-8 h-8 transition-colors border rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 border-purple-200">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                                        </a>

                                        {{-- Toggle activo --}}
                                        @if(!$esYo)
                                            <form action="{{ route('vendedores.toggle', $vendedor) }}?{{ http_build_query($query) }}" method="POST" class="inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" title="{{ $vendedor->activo ? 'Inactivar' : 'Activar' }}"
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg transition-colors border
                                                        {{ $vendedor->activo ? 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100 border-yellow-200' : 'bg-green-50 text-green-600 hover:bg-green-100 border-green-200' }}">
                                                    @if($vendedor->activo)
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                                                    @else
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    @endif
                                                </button>
                                            </form>
                                        @else
                                            <span title="Tu usuario" class="inline-flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            </span>
                                        @endif

                                        {{-- Eliminar --}}
                                        @if(!$esYo && !$esAdmin && ($vendedor->puede_eliminar ?? true))
                                            <form action="{{ route('vendedores.destroy', $vendedor) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar al usuario {{ addslashes($vendedor->name) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Eliminar"
                                                    class="inline-flex items-center justify-center w-8 h-8 text-red-600 transition-colors border border-red-200 rounded-lg bg-red-50 hover:bg-red-100">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        @else
                                            <button type="button" title="{{ $esYo ? 'Tu usuario' : ($esAdmin ? 'Es administrador' : 'Tiene movimientos') }}"
                                                class="inline-flex items-center justify-center w-8 h-8 text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed bg-gray-50">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/></svg>
                                        <div class="font-medium text-gray-500">Sin resultados</div>
                                        @if(request()->hasAny(['nombre','rol','estado']))
                                            <a href="{{ route('vendedores.index') }}" class="text-sm text-indigo-600 hover:underline">Limpiar filtros</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>