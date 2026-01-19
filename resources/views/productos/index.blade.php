<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Lista de Productos') }}
        </h2>
    </x-slot>

    <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-4">
                    <a href="{{ route('productos.create') }}"
                        class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                        Agregar Producto
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border border-collapse border-gray-200 divide-y divide-gray-200">
                        <thead class="text-left bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 border">Nombre</th>
                                <th class="px-4 py-2 border">Marca</th>
                                <th class="px-4 py-2 border">Categoría</th>
                                <th class="px-4 py-2 border">Unidad</th>
                                <th class="px-4 py-2 border">Precio</th>
                                <th class="px-4 py-2 text-center border">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos as $producto)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">
                                        <div class="flex items-center gap-2">
                                            <span>{{ $producto->nombre }}</span>

                                            @if(!$producto->activo)
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-gray-200 text-gray-700">
                                                    Inactivo
                                                </span>
                                            @endif

                                            @if($producto->tiene_movimientos)
                                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-indigo-100 text-indigo-700">
                                                    Con movimientos
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-2 border">{{ $producto->marca }}</td>
                                    <td class="px-4 py-2 border">{{ $producto->categoria->nombre }}</td>
                                    <td class="px-4 py-2 border">{{ $producto->unidadMedida->nombre }}</td>
                                    <td class="px-4 py-2 border">${{ number_format($producto->precio, 2) }}</td>

                                    <td class="px-4 py-2 text-center border">
                                        <div class="flex justify-center gap-2">

                                            <a href="{{ route('productos.edit', $producto) }}"
                                                class="px-3 py-1 text-white bg-yellow-500 rounded hover:bg-yellow-700">
                                                Editar
                                            </a>

                                            {{-- ✅ Eliminar solo si NO tiene movimientos --}}
                                            @if($producto->puede_eliminar)
                                                <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                                    onsubmit="return confirm('¿Eliminar producto definitivamente?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-700">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button"
                                                    class="px-3 py-1 text-white bg-red-300 rounded cursor-not-allowed"
                                                    title="No se puede eliminar porque ya tiene movimientos (ventas/traslados/producción/inventario).">
                                                    Eliminar
                                                </button>
                                            @endif

                                            {{-- ✅ Toggle activo/inactivo --}}
                                            <form action="{{ route('productos.toggle', $producto) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1 rounded text-xs font-semibold
                                                    {{ $producto->activo ? 'bg-yellow-500 text-white' : 'bg-green-600 text-white' }}">
                                                    {{ $producto->activo ? 'Inactivar' : 'Activar' }}
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                        No hay productos registrados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
