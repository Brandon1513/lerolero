<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('vendedores.index') }}"
                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Editar Usuario</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $vendedor->name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl py-8 mx-auto sm:px-6 lg:px-8">

        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 mb-5 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('vendedores.update', $vendedor) }}" class="space-y-4">
            @csrf
            @method('PUT')

            {{-- Datos básicos --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-700">Datos del usuario</h3>
                    @if($vendedor->activo)
                        <span class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activo</span>
                    @else
                        <span class="ml-auto inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactivo</span>
                    @endif
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nombre completo <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $vendedor->name) }}"
                            placeholder="Ej. Juan Pérez" required autofocus
                            class="w-full px-3 py-2 text-sm border rounded-lg outline-none transition focus:ring-2 focus:ring-indigo-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"/>
                        @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Correo electrónico <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $vendedor->email) }}"
                            placeholder="Ej. juan@correo.com" required
                            class="w-full px-3 py-2 text-sm border rounded-lg outline-none transition focus:ring-2 focus:ring-indigo-500 {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"/>
                        @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Contraseña --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-700">Contraseña</h3>
                    <span class="ml-auto text-xs text-gray-400">Deja en blanco para no cambiar</span>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nueva contraseña (opcional)</label>
                        <div class="relative">
                            <input type="password" id="password" name="password"
                                placeholder="Mínimo 8 caracteres"
                                class="w-full pr-10 px-3 py-2 text-sm border rounded-lg outline-none transition focus:ring-2 focus:ring-indigo-500 {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"/>
                            <button type="button" onclick="togglePassword('password', this)" class="absolute text-gray-400 -translate-y-1/2 right-3 top-1/2 hover:text-gray-600">
                                <svg class="w-4 h-4 icon-eye" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg class="hidden w-4 h-4 icon-eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Confirmar contraseña</label>
                        <div class="relative">
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full px-3 py-2 pr-10 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                            <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute text-gray-400 -translate-y-1/2 right-3 top-1/2 hover:text-gray-600">
                                <svg class="w-4 h-4 icon-eye" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg class="hidden w-4 h-4 icon-eye-off" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Roles — dinámicos desde la BD --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-700">Roles asignados</h3>
                </div>
                <div class="p-5">
                    @php
                        $rolesActuales = old('roles', $vendedor->getRoleNames()->toArray());
                        $rolesDescripciones = [
                            'administrador'    => ['desc' => 'Acceso total al sistema',        'icono' => '👑'],
                            'vendedor'         => ['desc' => 'Gestión de ventas y rutas',       'icono' => '🧑‍💼'],
                            'empleado_interno' => ['desc' => 'Crear producciones y traslados',  'icono' => '🏭'],
                        ];
                    @endphp
                    <div class="flex flex-wrap gap-3">
                        @foreach($roles as $role)
                            @php
                                $info = $rolesDescripciones[$role->name] ?? ['desc' => 'Sin descripción', 'icono' => '👤'];
                                $checked = in_array($role->name, $rolesActuales);
                            @endphp
                            <label class="flex items-center gap-3 px-4 py-3 border rounded-xl cursor-pointer transition-all
                                {{ $checked ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                                <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                    {{ $checked ? 'checked' : '' }}
                                    {{ $role->name === 'administrador' ? 'onchange=handleAdminCheckbox(this)' : '' }}
                                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div>
                                    <div class="text-sm font-semibold text-gray-800">{{ $info['icono'] }} {{ ucfirst(str_replace('_', ' ', $role->name)) }}</div>
                                    <div class="text-xs text-gray-500">{{ $info['desc'] }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')<p class="mt-2 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    Guardar Cambios
                </button>
                <a href="{{ route('vendedores.index') }}"
                    class="flex-1 py-2.5 text-center bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
function handleAdminCheckbox(input) {
    if (input.checked) {
        const ok = confirm(' Estás a punto de asignar el rol de ADMINISTRADOR. ¿Estás seguro?');
        if (!ok) input.checked = false;
    }
}
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPass = input.type === 'password';
    input.type = isPass ? 'text' : 'password';
    btn.querySelector('.icon-eye').classList.toggle('hidden', isPass);
    btn.querySelector('.icon-eye-off').classList.toggle('hidden', !isPass);
}
</script>