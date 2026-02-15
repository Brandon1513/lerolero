<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('productos.index') }}"
                    class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Editar Producto</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $producto->nombre }}</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full
                {{ $producto->activo ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                <span class="w-1.5 h-1.5 rounded-full {{ $producto->activo ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                {{ $producto->activo ? 'Activo' : 'Inactivo' }}
            </span>
        </div>
    </x-slot>

    <div class="max-w-4xl py-8 mx-auto sm:px-6 lg:px-8">

        {{-- Errores globales --}}
        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 mb-6 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <div class="mb-1 font-semibold">Hay errores en el formulario:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- ── COLUMNA IZQUIERDA (2/3) ── --}}
                <div class="space-y-5 lg:col-span-2">

                    {{-- Card: Info básica --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Información básica
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">

                            {{-- Nombre --}}
                            <div>
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Nombre del Producto <span class="text-red-500">*</span>
                                </label>
                                <input id="nombre" name="nombre" type="text"
                                    value="{{ old('nombre', $producto->nombre) }}"
                                    required autofocus
                                    class="w-full px-3 py-2.5 text-sm border rounded-lg outline-none transition
                                        {{ $errors->has('nombre') ? 'border-red-400 bg-red-50 focus:ring-2 focus:ring-red-300' : 'border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500' }}"/>
                                @error('nombre')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Marca --}}
                            <div>
                                <label for="marca" class="block text-sm font-medium text-gray-700 mb-1.5">Marca</label>
                                <input id="marca" name="marca" type="text"
                                    value="{{ old('marca', $producto->marca) }}"
                                    placeholder="Ej. LeroLero"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"/>
                                @error('marca')
                                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Categoría + Unidad en grid --}}
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Categoría <span class="text-red-500">*</span>
                                    </label>
                                    <select name="categoria_id" id="categoria_id"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                                        <option value="">-- Selecciona --</option>
                                        @foreach($categorias as $categoria)
                                            <option value="{{ $categoria->id }}"
                                                {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="unidad_medida_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                        Unidad de Medida <span class="text-red-500">*</span>
                                    </label>
                                    <select name="unidad_medida_id" id="unidad_medida_id"
                                        class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                                        <option value="">-- Selecciona --</option>
                                        @foreach($unidades as $unidad)
                                            <option value="{{ $unidad->id }}"
                                                {{ old('unidad_medida_id', $producto->unidad_medida_id) == $unidad->id ? 'selected' : '' }}>
                                                {{ $unidad->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unidad_medida_id')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Card: Precios por nivel --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                Precios por Nivel de Cliente
                            </h3>
                            <p class="text-xs text-gray-500 mt-0.5">Deja vacío para usar el precio base en ese nivel</p>
                        </div>
                        <div class="p-5">
                            @php
                                $map = $producto->preciosNivel->pluck('precio', 'nivel_precio_id');
                            @endphp
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($niveles as $nivel)
                                    <div class="relative">
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                                            {{ $nivel->nombre }}
                                        </label>
                                        <div class="relative">
                                            <span class="absolute text-sm font-medium text-gray-400 -translate-y-1/2 left-3 top-1/2">$</span>
                                            <input
                                                id="niveles[{{ $nivel->id }}]"
                                                name="niveles[{{ $nivel->id }}]"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                placeholder="0.00"
                                                value="{{ old('niveles.'.$nivel->id, $map[$nivel->id] ?? '') }}"
                                                class="w-full pl-7 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"/>
                                        </div>
                                        @error('niveles.'.$nivel->id)
                                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── COLUMNA DERECHA (1/3) ── --}}
                <div class="space-y-5">

                    {{-- Card: Precio base --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Precio base
                            </h3>
                        </div>
                        <div class="p-5">
                            <label for="precio" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Precio Base <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute text-sm font-semibold text-gray-500 -translate-y-1/2 left-3 top-1/2">$</span>
                                <input id="precio" name="precio" type="number" step="0.01" min="0"
                                    value="{{ old('precio', $producto->precio) }}"
                                    required
                                    class="w-full pl-7 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition font-semibold"/>
                            </div>
                            @error('precio')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-400">Se aplica cuando no hay precio por nivel definido.</p>
                        </div>
                    </div>

                    {{-- Card: Imagen --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Imagen
                            </h3>
                        </div>
                        <div class="p-5">
                            {{-- Preview imagen actual --}}
                            @if($producto->imagen)
                                <div class="flex items-center justify-center mb-3 overflow-hidden border border-gray-200 rounded-lg bg-gray-50" style="height:140px">
                                    <img src="{{ asset('storage/'.$producto->imagen) }}"
                                        alt="{{ $producto->nombre }}"
                                        class="object-contain max-w-full max-h-full"
                                        onerror="this.parentElement.innerHTML='<span class=\'text-xs text-gray-400\'>Sin imagen</span>'"/>
                                </div>
                            @else
                                <div class="flex items-center justify-center mb-3 border-2 border-gray-200 border-dashed rounded-lg" style="height:100px">
                                    <div class="text-center">
                                        <svg class="w-8 h-8 mx-auto mb-1 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"/>
                                        </svg>
                                        <span class="text-xs text-gray-400">Sin imagen</span>
                                    </div>
                                </div>
                            @endif

                            <label for="imagen"
                                class="flex items-center justify-center gap-2 w-full px-3 py-2.5 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                <span id="file-label">{{ $producto->imagen ? 'Cambiar imagen' : 'Subir imagen' }}</span>
                            </label>
                            <input type="file" name="imagen" id="imagen" accept="image/*" class="hidden"
                                onchange="document.getElementById('file-label').textContent = this.files[0]?.name ?? 'Subir imagen'"/>

                            @error('imagen')
                                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex flex-col gap-2">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar cambios
                        </button>
                        <a href="{{ route('productos.index') }}"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-gray-700 text-sm font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>
</x-app-layout>