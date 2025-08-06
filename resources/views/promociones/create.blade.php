<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Crear Promoción') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-4 text-sm text-red-600">
                            <ul class="pl-5 list-disc">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('promociones.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="nombre" value="Nombre de la Promoción" />
                            <x-text-input id="nombre" name="nombre" type="text" class="block w-full mt-1" required />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="descripcion" value="Descripción" />
                            <textarea id="descripcion" name="descripcion" rows="3"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                            </textarea>
                        </div>
                        <!-- Fecha de Inicio -->
                        <div class="mb-4">
                            <x-input-label for="fecha_inicio" :value="__('Fecha de Inicio')" />
                            <x-text-input type="date" id="fecha_inicio" name="fecha_inicio" class="block w-full mt-1" :value="old('fecha_inicio')" />
                        </div>

                        <!-- Fecha de Fin -->
                        <div class="mb-4">
                            <x-input-label for="fecha_fin" :value="__('Fecha de Fin')" />
                            <x-text-input type="date" id="fecha_fin" name="fecha_fin" class="block w-full mt-1" :value="old('fecha_fin')" />
                        </div>


                        <div class="mb-4">
                            <x-input-label for="precio_promocional" value="Precio Promocional" />
                            <x-text-input id="precio_promocional" name="precio_promocional" type="number" step="0.01" min="0" class="block w-full mt-1" required />
                        </div>

                        {{-- Selector de productos con búsqueda --}}
<div class="mb-4">
    <label for="producto_selector" class="block text-sm font-medium text-gray-700">Seleccionar Producto</label>
    <select id="producto_selector" class="w-full">
        <option value="">-- Buscar producto --</option>
        @foreach ($productos as $producto)
            <option value="{{ $producto->id }}">{{ $producto->nombre }}</option>
        @endforeach
    </select>
</div>

{{-- Tabla dinámica de productos seleccionados --}}
<div class="mb-4">
    <table class="w-full text-sm border border-collapse" id="tabla-productos">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-2 py-1 border">Producto</th>
                <th class="px-2 py-1 border">Cantidad</th>
                <th class="px-2 py-1 border">Eliminar</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


                        <x-primary-button>
                            Guardar Promoción
                        </x-primary-button>

                        <a href="{{ route('promociones.index') }}"
                            class="inline-flex items-center px-4 py-2 ml-4 text-xs font-semibold tracking-widest text-gray-700 uppercase bg-gray-300 border border-transparent rounded-md hover:bg-gray-400">
                            Cancelar
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    const productosData = @json($productos);

    document.getElementById('producto_selector').addEventListener('change', function () {
        const id = this.value;
        const nombre = this.options[this.selectedIndex].text;

        if (!id) return;

        // Evitar duplicados
        if (document.getElementById(`producto-row-${id}`)) return;

        const tbody = document.querySelector('#tabla-productos tbody');
        const row = document.createElement('tr');
        row.id = `producto-row-${id}`;
        row.innerHTML = `
            <td class="px-2 py-1 border">
                ${nombre}
                <input type="hidden" name="productos[${id}][id]" value="${id}">
            </td>
            <td class="px-2 py-1 border">
                <input type="number" name="productos[${id}][cantidad]" value="1" min="1" class="w-20 p-1 border rounded">
            </td>
            <td class="px-2 py-1 text-center border">
                <button type="button" onclick="this.closest('tr').remove()">❌</button>
            </td>
        `;
        tbody.appendChild(row);

        // Reiniciar selector
        this.value = '';
    });
</script>

