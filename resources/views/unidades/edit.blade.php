<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Unidad de Medida') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-xl mx-auto">
        <form method="POST" action="{{ route('unidades.update', $unidad) }}">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div class="mb-4">
                <x-input-label for="nombre" value="Nombre de la Unidad" />
                <x-text-input id="nombre" class="block w-full mt-1" name="nombre" type="text"
                    value="{{ old('nombre', $unidad->nombre) }}" required autofocus />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <!-- Equivalencia -->
            <div class="mb-4">
                <x-input-label for="equivalente" value="equivalente" />
                <x-text-input id="equivalente" class="block w-full mt-1" name="equivalente" type="number" min="1" step="1"
                    value="{{ old('equivalente', $unidad->equivalente) }}" required />
                <x-input-error :messages="$errors->get('equivalente')" class="mt-2" />
            </div>

            <!-- Botón -->
            <x-primary-button>
                Actualizar
            </x-primary-button>
        </form>
    </div>
</x-app-layout>
