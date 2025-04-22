<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Registrar Nueva Venta</h2>
    </x-slot>

    <div class="max-w-4xl py-12 mx-auto">
        <form method="POST" action="{{ route('ventas.store') }}">
            @csrf

            <div class="mb-4">
                <x-input-label for="cliente_id" value="Cliente" />
                <select name="cliente_id" id="cliente_id" class="block w-full mt-1">
                    <option value="">-- Selecciona uno --</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('cliente_id')" class="mt-2" />
            </div>

            <div class="mb-4">
                <x-input-label for="fecha" value="Fecha de la venta" />
                <x-text-input name="fecha" id="fecha" type="date" class="block w-full mt-1" value="{{ now()->toDateString() }}" required />
                <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
            </div>

            <div class="mb-6">
                <x-input-label for="observaciones" value="Observaciones" />
                <textarea name="observaciones" id="observaciones" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                <x-input-error :messages="$errors->get('observaciones')" class="mt-2" />
            </div>

            <div class="mb-6">
                <h3 class="mb-2 text-lg font-semibold text-gray-700">Productos</h3>
                @foreach ($productos as $producto)
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-1/2">
                            <label class="block font-medium text-gray-700">{{ $producto->nombre }}</label>
                        </div>
                        <div class="w-1/4">
                            <input type="number" name="productos[{{ $producto->id }}][cantidad]" min="0" placeholder="Cantidad" class="w-full px-2 py-1 border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <div class="w-1/4">
                            <input type="number" name="productos[{{ $producto->id }}][precio_unitario]" min="0" step="0.01" placeholder="Precio $" class="w-full px-2 py-1 border-gray-300 rounded-md shadow-sm" />
                        </div>
                    </div>
                @endforeach
                <x-input-error :messages="$errors->get('productos')" class="mt-2" />
            </div>

            <x-primary-button>Guardar Venta</x-primary-button>
        </form>
    </div>
</x-app-layout>
