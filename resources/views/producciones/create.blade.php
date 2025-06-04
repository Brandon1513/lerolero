<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Registrar Nueva Producción</h2>
    </x-slot>

    <div class="max-w-4xl py-12 mx-auto sm:px-6 lg:px-8">
        <!-- Mensaje de error -->
        @if ($errors->any())
            <div class="mb-4 text-red-600 bg-red-100 border border-red-400 rounded p-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('producciones.store') }}" method="POST" class="space-y-6 bg-white p-6 rounded shadow">
            @csrf

            <!-- Producto -->
            <div>
                <x-input-label for="producto_id" value="Producto" />
                <select name="producto_id" id="producto_id" required class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    <option value="">-- Selecciona un producto --</option>
                    @foreach ($productos as $producto)
                        <option value="{{ $producto->id }}" {{ old('producto_id') == $producto->id ? 'selected' : '' }}>
                            {{ $producto->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Cantidad -->
            <div>
                <x-input-label for="cantidad" value="Cantidad Producida" />
                <x-text-input type="number" name="cantidad" id="cantidad" class="block w-full mt-1" min="1" required value="{{ old('cantidad') }}" />
            </div>

            <!-- Fecha -->
            <div>
                <x-input-label for="fecha" value="Fecha de Producción" />
                <x-text-input type="date" name="fecha" id="fecha" class="block w-full mt-1" required value="{{ old('fecha', now()->toDateString()) }}" />
            </div>

            <!-- Lote (opcional) -->
            <div>
                <x-input-label for="lote" value="Lote (opcional)" />
                <x-text-input type="text" name="lote" id="lote" class="block w-full mt-1" value="{{ old('lote') }}" />
            </div>
            <!-- Fecha de caducidad -->
            <div>
                <x-input-label for="fecha_caducidad" value="Fecha de Caducidad" />
                <x-text-input type="date" name="fecha_caducidad" id="fecha_caducidad" class="block w-full mt-1"
                    required value="{{ old('fecha_caducidad') }}" />
            </div>

            <!-- Notas -->
            <div>
                <x-input-label for="notas" value="Notas" />
                <textarea name="notas" id="notas" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('notas') }}</textarea>
            </div>

            <!-- Botón -->
            <div class="flex justify-end gap-4">
                <a href="{{ route('producciones.index') }}"
                   class="px-4 py-2 text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md">
                    Cancelar
                </a>
                <x-primary-button>Guardar Producción</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
