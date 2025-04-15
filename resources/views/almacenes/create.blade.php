<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Crear Almacén
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-12">
        <form method="POST" action="{{ route('almacenes.store') }}">
            @csrf

            <div class="mb-4">
                <x-input-label value="Nombre" />
                <x-text-input name="nombre" class="block w-full mt-1" required />
            </div>

            <div class="mb-4">
                <x-input-label value="Descripción" />
                <x-text-input name="descripcion" class="block w-full mt-1" />
            </div>

            <div class="mb-4">
                <x-input-label value="Ubicación" />
                <x-text-input name="ubicacion" class="block w-full mt-1" />
            </div>

            <div class="mb-4">
                <x-input-label value="Tipo de Almacén" />
                <select name="tipo" id="tipo" class="block w-full mt-1" onchange="document.getElementById('vendedor-field').classList.toggle('hidden', this.value !== 'vendedor')">
                    <option value="general">General</option>
                    <option value="vendedor">Vendedor</option>
                </select>
            </div>

            <div id="vendedor-field" class="mb-4 hidden">
                <x-input-label value="Asignar a Vendedor" />
                <select name="user_id" class="block w-full mt-1">
                    <option value="">-- Selecciona un vendedor --</option>
                    @foreach($vendedores as $v)
                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <x-input-label value="Activo" />
                <input type="checkbox" name="activo" value="1" checked />
            </div>

            <x-primary-button>
                Guardar Almacén
            </x-primary-button>
        </form>
    </div>
</x-app-layout>
