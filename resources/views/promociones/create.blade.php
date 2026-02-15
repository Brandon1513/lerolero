<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('promociones.index') }}"
                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Nueva Promoción</h2>
                <p class="text-sm text-gray-500 mt-0.5">Define productos, precio y vigencia</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl py-8 mx-auto sm:px-6 lg:px-8">

        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 mb-6 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <div><div class="mb-1 font-semibold">Errores en el formulario:</div><ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            </div>
        @endif

        <form action="{{ route('promociones.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- ── COLUMNA IZQUIERDA (2/3) ── --}}
                <div class="space-y-5 lg:col-span-2">

                    {{-- Info básica --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Información básica
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                                <input id="nombre" name="nombre" type="text" required autofocus
                                    value="{{ old('nombre') }}" placeholder="Ej. Pack Dulces Verano"
                                    class="w-full px-3 py-2.5 text-sm border rounded-lg outline-none transition {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500' }}"/>
                                @error('nombre')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                                <textarea id="descripcion" name="descripcion" rows="2" placeholder="Descripción opcional..."
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition resize-none">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Productos --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                                Productos incluidos
                            </h3>
                            <span id="contador-productos" class="text-xs text-gray-400">0 productos</span>
                        </div>
                        <div class="p-5">
                            {{-- Selector --}}
                            <div class="flex gap-2 mb-4">
                                <select id="producto_selector"
                                    class="flex-1 px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                    <option value="">— Selecciona un producto para agregar —</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" data-nombre="{{ $producto->nombre }}">{{ $producto->nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="agregarProducto()"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Agregar
                                </button>
                            </div>

                            {{-- Tabla productos seleccionados --}}
                            <div id="tabla-wrap">
                                <table class="w-full text-sm" id="tabla-productos">
                                    <thead>
                                        <tr class="border-b border-gray-200 bg-gray-50">
                                            <th class="px-3 py-2 text-xs font-semibold tracking-wide text-left text-gray-500 uppercase">Producto</th>
                                            <th class="px-3 py-2 text-xs font-semibold tracking-wide text-center text-gray-500 uppercase w-28">Cantidad</th>
                                            <th class="w-10 px-3 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody-productos" class="divide-y divide-gray-100">
                                        {{-- JS --}}
                                    </tbody>
                                </table>
                                <div id="empty-productos" class="py-8 text-sm text-center text-gray-400">
                                    No hay productos agregados aún.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── COLUMNA DERECHA (1/3) ── --}}
                <div class="space-y-5">

                    {{-- Precio --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Precio
                            </h3>
                        </div>
                        <div class="p-5">
                            <label for="precio_promocional" class="block text-sm font-medium text-gray-700 mb-1.5">Precio Promocional <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute text-sm font-semibold text-gray-500 -translate-y-1/2 left-3 top-1/2">$</span>
                                <input id="precio_promocional" name="precio_promocional" type="number" step="0.01" min="0" required
                                    value="{{ old('precio_promocional') }}" placeholder="0.00"
                                    class="w-full pl-7 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-semibold"/>
                            </div>
                            @error('precio_promocional')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Vigencia --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Vigencia
                            </h3>
                        </div>
                        <div class="p-5 space-y-3">
                            <div>
                                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1.5">Inicio</label>
                                <input type="date" id="fecha_inicio" name="fecha_inicio" value="{{ old('fecha_inicio') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"/>
                            </div>
                            <div>
                                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1.5">Fin</label>
                                <input type="date" id="fecha_fin" name="fecha_fin" value="{{ old('fecha_fin') }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"/>
                            </div>
                            <p class="text-xs text-gray-400">Deja vacío para sin fecha límite.</p>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex flex-col gap-2">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Guardar Promoción
                        </button>
                        <a href="{{ route('promociones.index') }}"
                            class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-white text-gray-700 text-sm font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
    function actualizarContador() {
        const filas = document.querySelectorAll('#tbody-productos tr').length;
        document.getElementById('contador-productos').textContent = `${filas} producto${filas !== 1 ? 's' : ''}`;
        document.getElementById('empty-productos').style.display = filas === 0 ? 'block' : 'none';
        document.querySelector('#tabla-productos thead').style.display = filas === 0 ? 'none' : '';
    }

    function agregarProducto() {
        const select = document.getElementById('producto_selector');
        const id = select.value;
        const nombre = select.options[select.selectedIndex]?.dataset.nombre ?? select.options[select.selectedIndex]?.text;

        if (!id || document.getElementById('producto_' + id)) return;

        const inicial = nombre.charAt(0).toUpperCase();
        const fila = document.createElement('tr');
        fila.id = 'producto_' + id;
        fila.className = 'hover:bg-gray-50 transition-colors';
        fila.innerHTML = `
            <td class="px-3 py-2.5">
                <input type="hidden" name="productos[${id}][id]" value="${id}">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 text-xs font-bold text-indigo-700 bg-indigo-100 rounded shrink-0">${inicial}</span>
                    <span class="text-sm text-gray-800">${nombre}</span>
                </div>
            </td>
            <td class="px-3 py-2.5 text-center">
                <input type="number" name="productos[${id}][cantidad]" value="1" min="1" required
                    class="w-20 px-2 py-1.5 text-sm text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-semibold"/>
            </td>
            <td class="px-3 py-2.5 text-center">
                <button type="button" onclick="eliminarProducto(${id})"
                    class="inline-flex items-center justify-center text-red-400 transition-colors rounded-lg w-7 h-7 hover:bg-red-50 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </td>
        `;
        document.getElementById('tbody-productos').appendChild(fila);
        select.value = '';
        actualizarContador();
    }

    function eliminarProducto(id) {
        const fila = document.getElementById('producto_' + id);
        if (fila) { fila.remove(); actualizarContador(); }
    }

    actualizarContador();
    </script>
</x-app-layout>