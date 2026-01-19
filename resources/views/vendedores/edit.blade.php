<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Vendedor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form method="POST" action="{{ route('vendedores.update', $vendedor) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nombre -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nombre')" />
                            <x-text-input id="name" name="name" type="text" class="block w-full mt-1"
                                :value="old('name', $vendedor->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Correo -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Correo electrónico')" />
                            <x-text-input id="email" name="email" type="email" class="block w-full mt-1"
                                :value="old('email', $vendedor->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Cambiar contraseña -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Nueva Contraseña (opcional)')" />

                            <div class="relative mt-1">
                                <x-text-input
                                    id="password"
                                    name="password"
                                    type="password"
                                    class="block w-full pr-12"
                                    placeholder="Dejar en blanco si no deseas cambiarla"
                                />

                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                                    onclick="togglePassword('password', this)"
                                    aria-label="Mostrar/Ocultar contraseña"
                                >
                                    <!-- eye -->
                                    <svg class="w-5 h-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <!-- eye-off -->
                                    <svg class="hidden w-5 h-5 icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3l18 18M10.477 10.477A3 3 0 0012 15a3 3 0 002.523-4.523M9.88 9.88A3 3 0 0115 12m6.542 0C20.268 16.057 16.477 19 12 19c-1.51 0-2.952-.334-4.25-.93M6.11 6.11C4.56 7.26 3.35 8.95 2.458 12c.51 1.625 1.322 3.058 2.37 4.23" />
                                    </svg>
                                </button>
                            </div>

                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />

                            <div class="relative mt-1">
                                <x-text-input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="block w-full pr-12"
                                />

                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700"
                                    onclick="togglePassword('password_confirmation', this)"
                                    aria-label="Mostrar/Ocultar confirmación"
                                >
                                    <!-- eye -->
                                    <svg class="w-5 h-5 icon-eye" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <!-- eye-off -->
                                    <svg class="hidden w-5 h-5 icon-eye-off" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3l18 18M10.477 10.477A3 3 0 0012 15a3 3 0 002.523-4.523M9.88 9.88A3 3 0 0115 12m6.542 0C20.268 16.057 16.477 19 12 19c-1.51 0-2.952-.334-4.25-.93M6.11 6.11C4.56 7.26 3.35 8.95 2.458 12c.51 1.625 1.322 3.058 2.37 4.23" />
                                    </svg>
                                </button>
                            </div>

                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <!-- Roles -->
                        <div class="mb-4">
                            <x-input-label :value="__('Roles asignados')" />
                            <div class="mt-2 space-y-2">
                                @foreach ($roles as $role)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500"
                                                {{ $vendedor->hasRole($role->name) ? 'checked' : '' }}
                                                {{ $role->name === 'administrador' ? 'onchange=handleAdminCheckbox(this)' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">{{ $role->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        </div>

                        <!-- Botón -->
                        <div class="flex justify-end mt-6">
                            <x-primary-button>
                                {{ __('Actualizar Vendedor') }}
                            </x-primary-button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function handleAdminCheckbox(input) {
        if (input.checked) {
            const confirmed = confirm('⚠️ Estás a punto de asignar el rol de ADMINISTRADOR. ¿Estás seguro?');
            if (!confirmed) input.checked = false;
        }
    }
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;

        const isPassword = input.getAttribute('type') === 'password';
        input.setAttribute('type', isPassword ? 'text' : 'password');

        const eye = btn.querySelector('.icon-eye');
        const eyeOff = btn.querySelector('.icon-eye-off');

        if (eye && eyeOff) {
            eye.classList.toggle('hidden', isPassword);
            eyeOff.classList.toggle('hidden', !isPassword);
        }
    }
</script>
