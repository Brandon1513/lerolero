<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ isset($categoria) ? 'Editar Categoría' : 'Agregar Categoría' }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-xl mx-auto">
        <form method="POST" action="{{ isset($categoria) ? route('categorias.update', $categoria) : route('categorias.store') }}">
            @csrf
            @if(isset($categoria))
                @method('PUT')
            @endif

            <div class="mb-4">
                <x-input-label for="nombre" value="Nombre de la Categoría" />
                <x-text-input id="nombre" class="block w-full mt-1" name="nombre" type="text"
                    value="{{ old('nombre', $categoria->nombre ?? '') }}" required autofocus />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <x-primary-button>
                {{ isset($categoria) ? 'Actualizar' : 'Guardar' }}
            </x-primary-button>
        </form>
    </div>
</x-app-layout>
