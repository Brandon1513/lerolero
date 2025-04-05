<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Registrar Vendedor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h2 class="mb-6 text-lg font-bold text-gray-700">
                        {{ __('Nuevo Vendedor') }}
                    </h2>

                    <form method="POST" action="{{ route('vendedores.store') }}">
                        @csrf

                        <!-- Nombre -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Nombre Completo')" />
                            <x-text-input id="name" name="name" type="text" class="block w-full mt-1"
                                :value="old('name')" placeholder="Ej. Juan Pérez" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Correo electrónico')" />
                            <x-text-input id="email" name="email" type="email" class="block w-full mt-1"
                                :value="old('email')" placeholder="Ej. juan@correo.com" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-4">
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <x-text-input id="password" name="password" type="password" class="block w-full mt-1"
                                placeholder="Mínimo 8 caracteres" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="mb-4">
                            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full mt-1"
                                required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                        <!-- Selección de Rol -->
                        
                        <div class="mb-4">
                            <x-input-label :value="__('Rol de Usuario')" />

                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="role" value="vendedor" class="text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                                        {{ old('role') === 'vendedor' ? 'checked' : '' }} required onclick="handleRoleChange(this)">
                                    <span class="ml-2 text-gray-700">Vendedor</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="role" value="administrador" class="text-indigo-600 rounded border-gray-300 focus:ring-indigo-500"
                                        {{ old('role') === 'administrador' ? 'checked' : '' }} onclick="handleRoleChange(this)">
                                    <span class="ml-2 text-gray-700">Administrador</span>
                                </label>
                            </div>

                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>
                        <!-- Botón -->
                        <div class="flex justify-end mt-4">
                            <x-primary-button>
                                {{ __('Registrar Vendedor') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function handleRoleChange(input) {
        if (input.value === 'administrador') {
            const confirmed = confirm('⚠️ Estás a punto de asignar el rol de ADMINISTRADOR. ¿Estás seguro?');
            if (!confirmed) {
                input.checked = false;
            }
        }
    }
</script>