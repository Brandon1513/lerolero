<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Producto') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto">
        <form method="POST" action="{{ route('productos.update', $producto) }}">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div class="mb-4">
                <x-input-label for="nombre" value="Nombre del Producto" />
                <x-text-input id="nombre" name="nombre" type="text" class="block w-full mt-1"
                    value="{{ old('nombre', $producto->nombre) }}" required autofocus />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            <!-- Marca -->
            <div class="mb-4">
                <x-input-label for="marca" value="Marca" />
                <x-text-input id="marca" name="marca" type="text" class="block w-full mt-1"
                    value="{{ old('marca', $producto->marca) }}" />
                <x-input-error :messages="$errors->get('marca')" class="mt-2" />
            </div>

            <!-- Categoría -->
            <div class="mb-4">
                <x-input-label for="categoria_id" value="Categoría" />
                <select name="categoria_id" id="categoria_id" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
            </div>

            <!-- Unidad de Medida -->
            <div class="mb-4">
                <x-input-label for="unidad_medida_id" value="Unidad de Medida" />
                <select name="unidad_medida_id" id="unidad_medida_id" class="block w-full mt-1 rounded-md border-gray-300 shadow-sm">
                    @foreach ($unidades as $unidad)
                        <option value="{{ $unidad->id }}" {{ $producto->unidad_medida_id == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('unidad_medida_id')" class="mt-2" />
            </div>

            <!-- Precio -->
            <div class="mb-4">
                <x-input-label for="precio" value="Precio Base ($)" />
                <x-text-input id="precio" name="precio" type="number" step="0.01" class="block w-full mt-1"
                    value="{{ old('precio', $producto->precio) }}" required />
                <x-input-error :messages="$errors->get('precio')" class="mt-2" />
            </div>

            <!-- Fecha de Caducidad -->
            <div class="mb-4">
                <x-input-label for="fecha_caducidad" value="Fecha de Caducidad" />
                <x-text-input id="fecha_caducidad" name="fecha_caducidad" type="date" class="block w-full mt-1"
                    value="{{ old('fecha_caducidad', $producto->fecha_caducidad ? $producto->fecha_caducidad->format('Y-m-d') : '') }}" />
                <x-input-error :messages="$errors->get('fecha_caducidad')" class="mt-2" />
            </div>

            <!-- Cantidad -->
            <div class="mb-4">
                <x-input-label for="cantidad" value="Cantidad" />
                <x-text-input id="cantidad" name="cantidad" type="number" step="1" min="0" class="block w-full mt-1"
                    value="{{ old('cantidad', $producto->cantidad) }}" required />
                <x-input-error :messages="$errors->get('cantidad')" class="mt-2" />
            </div>

            <div class="flex justify-end mt-6">
                <x-primary-button>
                    Actualizar
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
