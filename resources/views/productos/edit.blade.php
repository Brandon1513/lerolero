<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Producto') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl py-12 mx-auto">
        <form method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data">
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
                <select name="categoria_id" id="categoria_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
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
                <select name="unidad_medida_id" id="unidad_medida_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
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

            <!-- Imagen Actual -->
            @if ($producto->imagen)
                <div class="mb-4">
                    <x-input-label value="Imagen Actual" />
                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="Imagen del producto" class="object-cover w-32 h-32 rounded-md">
                </div>
            @endif

            <!-- Nueva Imagen -->
            <div class="mb-4">
                <x-input-label for="imagen" value="Nueva Imagen (opcional)" />
                <x-text-input id="imagen" name="imagen" type="file" class="block w-full mt-1" accept="image/*" />
                <x-input-error :messages="$errors->get('imagen')" class="mt-2" />
            </div>

            <!-- Fecha de Caducidad -->
            <div class="mb-4">
                <x-input-label for="fecha_caducidad" value="Fecha de Caducidad" />
                <x-text-input 
                id="fecha_caducidad" 
                name="fecha_caducidad" 
                type="date" 
                class="block w-full mt-1"
                value="{{ old('fecha_caducidad', $producto->fecha_caducidad instanceof \Carbon\Carbon ? $producto->fecha_caducidad->format('Y-m-d') : $producto->fecha_caducidad) }}"
            />
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
