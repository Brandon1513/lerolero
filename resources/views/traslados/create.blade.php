<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Registrar Traslado de Inventario</h2>
    </x-slot>

    <div class="max-w-5xl py-12 mx-auto">
        <form method="POST" action="{{ route('traslados.store') }}">
            @csrf

            <!-- Almacén Origen -->
            <div class="mb-4">
                <x-input-label for="almacen_origen_id" value="Almacén Origen" />
                <select name="almacen_origen_id" id="almacen_origen_id" class="block w-full mt-1">
                    <option value="">-- Selecciona uno --</option>
                    @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('almacen_origen_id')" class="mt-2" />
            </div>

            <!-- Almacén Destino -->
            <div class="mb-4">
                <x-input-label for="almacen_destino_id" value="Almacén Destino" />
                <select name="almacen_destino_id" id="almacen_destino_id" class="block w-full mt-1">
                    <option value="">-- Selecciona uno --</option>
                    @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('almacen_destino_id')" class="mt-2" />
            </div>

            <!-- Fecha -->
            <div class="mb-4">
                <x-input-label for="fecha" value="Fecha del Traslado" />
                <x-text-input name="fecha" id="fecha" type="date" class="block w-full mt-1" value="{{ now()->toDateString() }}" required />
            </div>

            <!-- Observaciones -->
            <div class="mb-4">
                <x-input-label for="observaciones" value="Observaciones" />
                <textarea name="observaciones" id="observaciones" rows="3" class="block w-full border-gray-300 rounded-md shadow-sm"></textarea>
            </div>

            <!-- Productos -->
            <div class="mb-4">
                <h3 class="mb-2 text-lg font-semibold text-gray-700">Productos a trasladar</h3>

                @foreach ($productos as $producto)
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-1/3">
                            <label class="block font-medium text-gray-700">{{ $producto->nombre }}</label>
                            <span class="text-xs text-gray-500 disponibilidad" data-producto="{{ $producto->id }}">
                                Disponible: {{ $inventario[$producto->id] ?? 0 }}
                            </span>
                        </div>

                        <div class="w-1/4">
                            <input type="number"
                                name="productos[{{ $producto->id }}]"
                                min="0"
                                max="{{ $inventario[$producto->id] ?? 0 }}"
                                class="w-full px-2 py-1 border-gray-300 rounded-md shadow-sm cantidad-input"
                                data-producto="{{ $producto->id }}"
                            />
                            <div class="hidden text-xs text-red-500" id="error-{{ $producto->id }}">
                                No puedes ingresar más de lo disponible.
                            </div>
                        </div>
                    </div>
                @endforeach



                <x-input-error :messages="$errors->get('productos')" class="mt-2" />
            </div>

            <x-primary-button id="btn-enviar" class="mt-6">
                Registrar Traslado
            </x-primary-button>
        </form>
    </div>
</x-app-layout>
@push('scripts')
<script>
    document.getElementById('almacen_origen_id').addEventListener('change', function () {
        const almacenId = this.value;

        fetch(`/inventario/almacen/${almacenId}`)
            .then(response => response.json())
            .then(data => {
                // Actualiza las cantidades disponibles
                document.querySelectorAll('.disponibilidad').forEach(span => {
                    const id = span.dataset.producto;
                    const cantidad = data[id] ?? 0;
                    span.textContent = 'Disponible: ' + cantidad;
                });

                // Actualiza los inputs "max"
                document.querySelectorAll('.cantidad-input').forEach(input => {
                    const id = input.dataset.producto;
                    const cantidad = data[id] ?? 0;
                    input.max = cantidad;
                });
            });
    });
</script>
@endpush

<script>
    // Verifica en tiempo real cada input
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.addEventListener('input', function () {
            const max = parseInt(this.max);
            const value = parseInt(this.value);
            const productoId = this.dataset.producto;
            const errorDiv = document.getElementById(`error-${productoId}`);

            if (value > max) {
                errorDiv.classList.remove('hidden');
                this.classList.add('border-red-500');
            } else {
                errorDiv.classList.add('hidden');
                this.classList.remove('border-red-500');
            }
        });
    });
</script>
