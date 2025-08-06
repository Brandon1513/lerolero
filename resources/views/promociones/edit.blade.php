<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Editar Promoción
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="p-6 bg-white rounded-lg shadow-md">

                <form method="POST" action="{{ route('promociones.update', $promocion) }}">
                    @if ($errors->any())
                        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                            <strong>⚠️ Se encontraron algunos errores en el formulario:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @csrf
                    @method('PUT')

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <x-input-label for="nombre" :value="__('Nombre de la Promoción')" />
                        <x-text-input id="nombre" name="nombre" type="text" class="block w-full mt-1"
                            value="{{ old('nombre', $promocion->nombre) }}" required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    {{-- Descripción --}}
                    <div class="mb-4">
                        <x-input-label for="descripcion" :value="__('Descripción')" />
                        <textarea id="descripcion" name="descripcion" rows="3"
                            class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('descripcion', $promocion->descripcion) }}</textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                    </div>

                    {{-- Precio --}}
                    <div class="mb-4">
                        <x-input-label for="precio" :value="__('Precio Promocional')" />
                        <x-text-input id="precio" name="precio" type="number" step="0.01"
                            class="block w-full mt-1" value="{{ old('precio', $promocion->precio) }}" required />
                        <x-input-error :messages="$errors->get('precio')" class="mt-2" />

                    </div>

                    {{-- Fechas --}}
                    <div>
                        <x-input-label for="fecha_inicio" :value="__('Fecha de Inicio')" />
                        <x-text-input id="fecha_inicio" name="fecha_inicio" type="date" class="block w-full mt-1"
                            value="{{ old('fecha_inicio', $promocion->fecha_inicio ? \Illuminate\Support\Carbon::parse($promocion->fecha_inicio)->format('Y-m-d') : '') }}" />
                        <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="fecha_fin" :value="__('Fecha de Fin')" />
                        <x-text-input id="fecha_fin" name="fecha_fin" type="date" class="block w-full mt-1"
                            value="{{ old('fecha_fin', $promocion->fecha_fin ? \Illuminate\Support\Carbon::parse($promocion->fecha_fin)->format('Y-m-d') : '') }}" />
                        <x-input-error :messages="$errors->get('fecha_fin')" class="mt-2" />
                    </div>


                    {{-- Productos --}}
                    <div class="mt-4">
                        <x-input-label :value="__('Seleccionar Producto')" />

                        <div class="flex gap-2 mb-2">
                            <select id="producto_selector" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Buscar producto --</option>
                                @foreach($productos as $producto)
                                    <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
                                @endforeach
                            </select>
                            <button type="button" onclick="agregarProducto()" class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                Agregar
                            </button>
                        </div>

                        <table class="w-full text-sm border border-collapse border-gray-300">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-2 py-1 border">Producto</th>
                                    <th class="px-2 py-1 border">Cantidad</th>
                                    <th class="px-2 py-1 border">Eliminar</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-productos">
                                @foreach ($productosSeleccionados as $id => $cantidad)
    @php $producto = $productos->firstWhere('id', $id); @endphp
    @if ($producto)
        <tr id="producto_{{ $id }}">
            <td class="px-2 py-1 border">
                <input type="hidden" name="productos[{{ $id }}][id]" value="{{ $id }}">
                {{ $producto->nombre }}
            </td>
            <td class="px-2 py-1 border">
                <input type="number" name="productos[{{ $id }}][cantidad]" value="{{ $cantidad }}"
                    class="w-20 border-gray-300 rounded-md" min="1" required>
            </td>
            <td class="px-2 py-1 text-center border">
                <button type="button" onclick="eliminarProducto({{ $id }})" class="text-red-600 hover:text-red-800">✘</button>
            </td>
        </tr>
    @endif
@endforeach


                            </tbody>
                        </table>
                    </div>



                    {{-- Botones --}}
                    <div class="flex justify-end gap-4 mt-6">
                        <x-primary-button>
                            Guardar Cambios
                        </x-primary-button>

                        <a href="{{ route('promociones.index') }}"
                            class="px-4 py-2 text-gray-700 bg-gray-300 rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    function agregarProducto() {
        const select = document.getElementById('producto_selector');
        const id = select.value;
        const nombre = select.options[select.selectedIndex].text;

        if (!id || document.getElementById('producto_' + id)) return;

        const fila = `
            <tr id="producto_${id}">
                <td class="px-2 py-1 border">
                    <input type="hidden" name="productos[${id}][id]" value="${id}">
                    ${nombre}
                </td>
                <td class="px-2 py-1 border">
                    <input type="number" name="productos[${id}][cantidad]" value="1" class="w-20 border-gray-300 rounded-md" min="1" required>
                </td>
                <td class="px-2 py-1 text-center border">
                    <button type="button" onclick="eliminarProducto(${id})" class="text-red-600 hover:text-red-800">✘</button>
                </td>
            </tr>
        `;

        document.getElementById('tabla-productos').insertAdjacentHTML('beforeend', fila);
    }

    function eliminarProducto(id) {
        const fila = document.getElementById('producto_' + id);
        if (fila) fila.remove();
    }
</script>
