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
                <select name="almacen_origen_id" id="almacen_origen_id" class="block w-full mt-1" required>
                    <option value="">-- Selecciona uno --</option>
                    @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Almacén Destino -->
            <div class="mb-4">
                <x-input-label for="almacen_destino_id" value="Almacén Destino" />
                <select name="almacen_destino_id" id="almacen_destino_id" class="block w-full mt-1" required>
                    <option value="">-- Selecciona uno --</option>
                    @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                </select>
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

            <!-- Productos por Lote -->
            <div class="mb-4">
                <h3 class="mb-2 text-lg font-semibold text-gray-700">Productos a trasladar</h3>

                <div id="contenedor-productos">
                    <!-- Aquí se cargarán los lotes dinámicamente vía JS -->
                    <p class="text-gray-500">Selecciona un almacén origen para ver los productos disponibles.</p>
                </div>
            </div>

            <<x-primary-button type="submit" class="mt-6">
                Registrar Traslado
            </x-primary-button>
        </form>
    </div>
</x-app-layout>


<script>
document.getElementById('almacen_origen_id').addEventListener('change', function () {
    const almacenId = this.value;
    const contenedor = document.getElementById('contenedor-productos');

    if (!almacenId) {
        contenedor.innerHTML = '<p class="text-gray-500">Selecciona un almacén origen para ver los productos disponibles.</p>';
        return;
    }

    fetch(`/traslados/lotes/${almacenId}`)
        .then(response => response.json())
        .then(data => {
            console.log("Lotes recibidos:", data); // 👈 Verifica si el JSON se carga

            contenedor.innerHTML = '';

            if (Object.keys(data).length === 0) {
                contenedor.innerHTML = '<p class="text-gray-500">No hay productos disponibles en este almacén.</p>';
                return;
            }

            Object.entries(data).forEach(([productoId, lotes]) => {
                const productoDiv = document.createElement('div');
                productoDiv.classList.add('mb-6');

                const productoLabel = document.createElement('label');
                productoLabel.classList.add('font-semibold', 'text-gray-700');
                productoLabel.textContent = lotes[0]?.producto ?? 'Producto sin nombre';
                productoDiv.appendChild(productoLabel);

                lotes.forEach((lote, index) => {
                    const fila = document.createElement('div');
                    fila.classList.add('flex', 'items-center', 'gap-4', 'mb-2', 'ml-4');

                    fila.innerHTML = `
                        <div class="w-1/3 text-sm text-gray-600">
                            Lote: <strong>${lote.lote}</strong> — Vence: <strong>${lote.fecha_caducidad}</strong>
                        </div>
                        <div class="w-1/6 text-sm text-gray-500">Disponible: ${lote.cantidad}</div>
                        <div class="w-1/6">
                            <input type="number" name="detalles[${productoId}][${index}][cantidad]" min="0" max="${lote.cantidad}" class="w-full px-2 py-1 border-gray-300 rounded shadow-sm" placeholder="Cantidad" />
                            <input type="hidden" name="detalles[${productoId}][${index}][lote]" value="${lote.lote}" />
                            <input type="hidden" name="detalles[${productoId}][${index}][fecha_caducidad]" value="${lote.fecha_caducidad}" />
                        </div>
                    `;
                    productoDiv.appendChild(fila);
                });

                contenedor.appendChild(productoDiv);
            });
        })
        .catch(err => {
            console.error("Error al cargar lotes:", err);
            contenedor.innerHTML = '<p class="text-red-500">Ocurrió un error al obtener los lotes.</p>';
        });
});
</script>

