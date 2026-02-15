<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('producciones.index') }}"
                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Nueva Producción</h2>
                <p class="text-sm text-gray-500 mt-0.5">Registra una entrada al almacén general</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl py-8 mx-auto sm:px-6 lg:px-8">

        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 mb-6 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <div class="mb-1 font-semibold">Hay errores en el formulario:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form action="{{ route('producciones.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- ── COLUMNA IZQUIERDA (2/3) ── --}}
                <div class="space-y-5 lg:col-span-2">

                    {{-- Card: Producto --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                                Producto producido
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">

                            <div>
                                <label for="producto_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Producto <span class="text-red-500">*</span>
                                </label>
                                <select name="producto_id" id="producto_id" required
                                    class="w-full px-3 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white
                                        {{ $errors->has('producto_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                                    <option value="">— Selecciona un producto —</option>
                                    @foreach($productos as $producto)
                                        <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                                            {{ $producto->nombre }}{{ $producto->marca ? ' · '.$producto->marca : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('producto_id')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Cantidad producida <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" name="cantidad" id="cantidad"
                                        min="1" required
                                        value="{{ old('cantidad') }}"
                                        placeholder="Ej. 500"
                                        class="w-full px-3 py-2.5 pr-12 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-semibold
                                            {{ $errors->has('cantidad') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"/>
                                    <span class="absolute text-xs font-medium text-gray-400 -translate-y-1/2 right-3 top-1/2">uds</span>
                                </div>
                                @error('cantidad')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Card: Lote y caducidad --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Trazabilidad
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Información de lote y caducidad para trazabilidad</p>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-2 gap-4">

                                <div>
                                    <label for="lote" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Lote
                                        <span class="ml-1 text-xs font-normal text-gray-400">opcional</span>
                                    </label>
                                    <input type="text" name="lote" id="lote"
                                        value="{{ old('lote') }}"
                                        placeholder="Ej. LOTE290525"
                                        class="w-full px-3 py-2.5 text-sm font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"/>
                                    @error('lote')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="fecha_caducidad" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Fecha de Caducidad <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="fecha_caducidad" id="fecha_caducidad"
                                        required
                                        value="{{ old('fecha_caducidad') }}"
                                        class="w-full px-3 py-2.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition
                                            {{ $errors->has('fecha_caducidad') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"/>
                                    @error('fecha_caducidad')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                            {{-- Preview caducidad --}}
                            <div id="preview-caducidad" class="hidden px-3 py-2 mt-3 text-xs font-semibold border rounded-lg"></div>
                        </div>
                    </div>

                    {{-- Card: Notas --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Notas
                            </h3>
                        </div>
                        <div class="p-5">
                            <textarea name="notas" id="notas" rows="3"
                                placeholder="Observaciones opcionales sobre esta producción..."
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none">{{ old('notas') }}</textarea>
                        </div>
                    </div>

                </div>

                {{-- ── COLUMNA DERECHA (1/3) ── --}}
                <div class="space-y-5">

                    {{-- Card: Fecha --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Fecha
                            </h3>
                        </div>
                        <div class="p-5">
                            <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Fecha de producción <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="fecha" id="fecha"
                                required
                                value="{{ old('fecha', now()->toDateString()) }}"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"/>
                            @error('fecha')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Card: Destino --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                                Destino
                            </h3>
                        </div>
                        <div class="p-5">
                            <div class="flex items-center gap-3 p-3 border border-green-200 rounded-lg bg-green-50">
                                <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg shrink-0">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-green-800">Almacén General</div>
                                    <div class="text-xs text-green-600">Destino fijo de toda producción</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex flex-col gap-2">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Producción
                        </button>
                        <a href="{{ route('producciones.index') }}"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-gray-700 text-sm font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    <script>
    // Preview de caducidad en tiempo real
    document.getElementById('fecha_caducidad').addEventListener('change', function () {
        const preview = document.getElementById('preview-caducidad');
        if (!this.value) { preview.classList.add('hidden'); return; }

        const hoy  = new Date();
        const cad  = new Date(this.value + 'T00:00:00');
        const dias = Math.floor((cad - hoy) / 86400000);

        preview.classList.remove('hidden',
            'bg-red-50','border-red-200','text-red-700',
            'bg-amber-50','border-amber-200','text-amber-700',
            'bg-green-50','border-green-200','text-green-700');

        if (dias < 0) {
            preview.classList.add('bg-red-50','border-red-200','text-red-700');
            preview.textContent = '⚠ Fecha de caducidad ya pasada';
        } else if (dias <= 30) {
            preview.classList.add('bg-amber-50','border-amber-200','text-amber-700');
            preview.textContent = `⚡ Vence en ${dias} días — caduca pronto`;
        } else {
            preview.classList.add('bg-green-50','border-green-200','text-green-700');
            preview.textContent = `✓ Válido por ${dias} días`;
        }
    });
    </script>
</x-app-layout>