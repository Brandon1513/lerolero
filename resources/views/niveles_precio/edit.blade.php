<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Editar Nivel de Precio</h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto">
        <form method="POST" action="{{ route('niveles-precio.update', $nivel) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <x-input-label for="nombre" value="Nombre" />
                <x-text-input id="nombre" name="nombre" class="block mt-1 w-full" value="{{ old('nombre', $nivel->nombre) }}" required />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <x-primary-button>Actualizar</x-primary-button>
        </form>
    </div>
</x-app-layout>
