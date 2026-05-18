<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('vendedores.index') }}"
               class="inline-flex items-center justify-center w-8 h-8 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Permisos — {{ $usuario->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Rol base:
                    @foreach($usuario->getRoleNames() as $rol)
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-700">{{ $rol }}</span>
                    @endforeach
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 mx-auto max-w-5xl sm:px-6 lg:px-8">
        {{-- Leyenda --}}
        <div class="flex flex-wrap gap-4 mb-6 p-4 bg-white rounded-xl border border-gray-200 shadow-sm text-sm">
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-indigo-500 inline-block"></span>
                <span class="text-gray-600">Permiso heredado del <strong>rol</strong> (no editable aquí)</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded bg-emerald-500 inline-block"></span>
                <span class="text-gray-600">Permiso <strong>extra</strong> dado directamente a este usuario</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-4 h-4 rounded border-2 border-gray-300 inline-block"></span>
                <span class="text-gray-600">Sin acceso</span>
            </div>
        </div>

        <form method="POST" action="{{ route('vendedores.permisos.update', $usuario) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                @php
                    $iconosModulo = [
                        'dashboard'      => '🏠',
                        'clientes'       => '👥',
                        'vendedores'     => '🧑‍💼',
                        'productos'      => '🍬',
                        'categorias'     => '🏷️',
                        'producciones'   => '🏭',
                        'inventario'     => '📦',
                        'almacenes'      => '🏗️',
                        'traslados'      => '🚚',
                        'ventas'         => '🧾',
                        'promociones'    => '🔥',
                        'cierres'        => '📋',
                        'niveles_precio' => '💰',
                        'unidades'       => '📏',
                        'ayuda'          => '📚',
                    ];
                    $labelsAccion = [
                        'ver'      => 'Ver',
                        'crear'    => 'Crear',
                        'editar'   => 'Editar',
                        'eliminar' => 'Eliminar',
                        'cuadrar'  => 'Cuadrar',
                        'liberar'  => 'Liberar',
                        'gestionar'=> 'Gestionar',
                    ];
                @endphp

                @foreach($modulos as $modulo => $acciones)
                @php
                    $icono = $iconosModulo[$modulo] ?? '🔧';
                    $nombreModulo = ucfirst(str_replace('_', ' ', $modulo));
                    // ¿Tiene algún permiso de este módulo (por rol o directo)?
                    $tieneAlguno = collect($acciones)->some(
                        fn($a) => in_array("{$modulo}.{$a}", $permisosEfectivos)
                    );
                @endphp
                <div class="bg-white rounded-xl border {{ $tieneAlguno ? 'border-indigo-200' : 'border-gray-200' }} shadow-sm overflow-hidden">
                    {{-- Header módulo --}}
                    <div class="flex items-center justify-between px-5 py-3 {{ $tieneAlguno ? 'bg-indigo-50' : 'bg-gray-50' }} border-b {{ $tieneAlguno ? 'border-indigo-100' : 'border-gray-100' }}">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">{{ $icono }}</span>
                            <span class="font-semibold text-gray-800">{{ $nombreModulo }}</span>
                        </div>
                        @if($tieneAlguno)
                            <span class="text-xs font-semibold text-indigo-600 bg-indigo-100 px-2 py-0.5 rounded-full">Con acceso</span>
                        @else
                            <span class="text-xs text-gray-400">Sin acceso</span>
                        @endif
                    </div>

                    {{-- Acciones --}}
                    <div class="px-5 py-4 flex flex-wrap gap-3">
                        @foreach($acciones as $accion)
                            @php
                                $permiso = "{$modulo}.{$accion}";
                                $porRol     = in_array($permiso, $permisosRol);
                                $directo    = in_array($permiso, $permisosDirectos);
                                $efectivo   = in_array($permiso, $permisosEfectivos);
                                $label      = $labelsAccion[$accion] ?? ucfirst($accion);
                            @endphp

                            @if($porRol)
                                {{-- Heredado del rol: toggle deshabilitado, siempre activo --}}
                                <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-indigo-50 border border-indigo-200 opacity-80 cursor-not-allowed select-none"
                                     title="Heredado del rol — para quitar este permiso cambia el rol del usuario">
                                    <div class="w-8 h-4 rounded-full bg-indigo-500 relative flex-shrink-0">
                                        <div class="absolute right-0.5 top-0.5 w-3 h-3 rounded-full bg-white shadow"></div>
                                    </div>
                                    <span class="text-xs font-semibold text-indigo-700">{{ $label }}</span>
                                    <span class="text-xs text-indigo-400">(rol)</span>
                                </div>
                            @else
                                {{-- Permiso directo: toggle editable --}}
                                <label class="flex items-center gap-2 px-3 py-2 rounded-lg border cursor-pointer transition-all
                                    {{ $directo ? 'bg-emerald-50 border-emerald-300' : 'bg-gray-50 border-gray-200 hover:border-gray-300' }}">
                                    <input type="checkbox"
                                           name="permisos[]"
                                           value="{{ $permiso }}"
                                           {{ $directo ? 'checked' : '' }}
                                           class="sr-only peer"
                                           onchange="this.closest('label').classList.toggle('bg-emerald-50', this.checked);
                                                     this.closest('label').classList.toggle('border-emerald-300', this.checked);
                                                     this.closest('label').classList.toggle('bg-gray-50', !this.checked);
                                                     this.closest('label').classList.toggle('border-gray-200', !this.checked);">
                                    {{-- Toggle visual --}}
                                    <div class="w-8 h-4 rounded-full transition-colors {{ $directo ? 'bg-emerald-500' : 'bg-gray-300' }} relative flex-shrink-0 peer-checked:bg-emerald-500"
                                         id="toggle-{{ $modulo }}-{{ $accion }}">
                                        <div class="absolute {{ $directo ? 'right-0.5' : 'left-0.5' }} top-0.5 w-3 h-3 rounded-full bg-white shadow transition-all"
                                             id="dot-{{ $modulo }}-{{ $accion }}"></div>
                                    </div>
                                    <span class="text-xs font-semibold {{ $directo ? 'text-emerald-700' : 'text-gray-600' }}">{{ $label }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="flex-1 py-3 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
                    💾 Guardar permisos
                </button>
                <a href="{{ route('vendedores.index') }}"
                   class="px-6 py-3 bg-gray-100 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
    // Animar los toggles al cambiar
    document.querySelectorAll('input[type=checkbox][name="permisos[]"]').forEach(cb => {
        cb.addEventListener('change', function() {
            const parts = this.value.split('.');
            const toggle = document.getElementById(`toggle-${parts[0]}-${parts[1]}`);
            const dot    = document.getElementById(`dot-${parts[0]}-${parts[1]}`);
            const label  = this.closest('label').querySelector('span.text-xs.font-semibold');

            if (toggle) toggle.classList.toggle('bg-emerald-500', this.checked);
            if (toggle) toggle.classList.toggle('bg-gray-300', !this.checked);
            if (dot) {
                dot.classList.toggle('right-0.5', this.checked);
                dot.classList.toggle('left-0.5', !this.checked);
            }
            if (label) {
                label.classList.toggle('text-emerald-700', this.checked);
                label.classList.toggle('text-gray-600', !this.checked);
            }
        });
    });
    </script>
    @endpush
</x-app-layout>