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

            {{-- Nombre --}}
            <div class="mb-4">
                <x-input-label for="nombre" value="Nombre del Producto" />
                <x-text-input id="nombre" name="nombre" type="text" class="block w-full mt-1"
                    value="{{ old('nombre', $producto->nombre) }}" required autofocus />
                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
            </div>

            {{-- Marca --}}
            <div class="mb-4">
                <x-input-label for="marca" value="Marca" />
                <x-text-input id="marca" name="marca" type="text" class="block w-full mt-1"
                    value="{{ old('marca', $producto->marca) }}" />
                <x-input-error :messages="$errors->get('marca')" class="mt-2" />
            </div>

            {{-- Categoría --}}
            <div class="mb-4">
                <x-input-label for="categoria_id" value="Categoría" />
                <select name="categoria_id" id="categoria_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Selecciona una categoría --</option>
                    @foreach ($categorias as $categoria)
                        <option value="{{ $categoria->id }}"
                            {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                            {{ $categoria->nombre }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
            </div>

            {{-- Unidad de Medida --}}
            <div class="mb-4">
                <x-input-label for="unidad_medida_id" value="Unidad de Medida" />
                <select name="unidad_medida_id" id="unidad_medida_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Selecciona una unidad --</option>
                    @foreach ($unidades as $unidad)
                        <option value="{{ $unidad->id }}"
                            {{ old('unidad_medida_id', $producto->unidad_medida_id) == $unidad->id ? 'selected' : '' }}>
                            {{ $unidad->nombre }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('unidad_medida_id')" class="mt-2" />
            </div>

            {{-- Precio base --}}
            <div class="mb-4">
                <x-input-label for="precio" value="Precio Base ($)" />
                <x-text-input id="precio" name="precio" type="number" step="0.01" class="block w-full mt-1"
                    value="{{ old('precio', $producto->precio) }}" required />
                <x-input-error :messages="$errors->get('precio')" class="mt-2" />
            </div>

            {{-- Precios por Nivel --}}
            <div class="mb-6">
                <h3 class="mb-2 text-lg font-semibold text-gray-800">Precios por Nivel de Cliente</h3>
                @php
                    // Mapa [nivel_id => precio] para precargar
                    $map = $producto->preciosNivel->pluck('precio', 'nivel_precio_id');
                @endphp
                @foreach ($niveles as $nivel)
                    <div class="mb-3">
                        <x-input-label for="niveles[{{ $nivel->id }}]" :value="$nivel->nombre" />
                        <x-text-input
                            id="niveles[{{ $nivel->id }}]"
                            name="niveles[{{ $nivel->id }}]"
                            type="number"
                            step="0.01"
                            min="0"
                            class="block w-full mt-1"
                            placeholder="Ej. 10.50"
                            value="{{ old('niveles.'.$nivel->id, $map[$nivel->id] ?? '') }}"
                        />
                        <x-input-error :messages="$errors->get('niveles.' . $nivel->id)" class="mt-2" />
                    </div>
                @endforeach
            </div>

            {{-- Imagen --}}
            <div class="mb-4">
                <x-input-label for="imagen" value="Imagen del Producto" />
                <input type="file" name="imagen" id="imagen" accept="image/*"
                       class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                <x-input-error :messages="$errors->get('imagen')" class="mt-2" />
                @if($producto->imagen)
                    <p class="mt-2 text-sm text-gray-600">Imagen actual: {{ $producto->imagen }}</p>
                @endif
            </div>

            <div class="flex justify-end mt-6">
                <x-primary-button>Guardar cambios</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
