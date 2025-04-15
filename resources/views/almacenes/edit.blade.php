<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Almacén') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto">
        <form action="{{ route('almacenes.update', $almacen) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <x-input-label for="nombre" value="Nombre" />
                <x-text-input id="nombre" name="nombre" type="text" value="{{ old('nombre', $almacen->nombre) }}" class="block w-full mt-1" required />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="tipo" value="Tipo" />
                <select name="tipo" id="tipo" class="block w-full mt-1">
                    <option value="general" {{ $almacen->tipo == 'general' ? 'selected' : '' }}>General</option>
                    <option value="vendedor" {{ $almacen->tipo == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                </select>
                <x-input-error :messages="$errors->get('tipo')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="ubicacion" value="Ubicación" />
                <x-text-input id="ubicacion" name="ubicacion" type="text" value="{{ old('ubicacion', $almacen->ubicacion) }}" class="block w-full mt-1" />
                <x-input-error :messages="$errors->get('ubicacion')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="user_id" value="Usuario Asignado (Solo si es de vendedor)" />
                <select name="user_id" id="user_id" class="block w-full mt-1">
                    <option value="">-- Selecciona un usuario --</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ $almacen->user_id == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
            </div>

            <div class="flex justify-end">
                <x-primary-button>
                    Actualizar
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
