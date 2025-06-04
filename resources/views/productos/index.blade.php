<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Lista de Productos') }}
        </h2>
    </x-slot>

    <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="mb-4">
                    <a href="{{ route('productos.create') }}"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
                        Agregar Producto
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border border-collapse border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-100 text-left">
                            <tr>
                                <th class="px-4 py-2 border">Nombre</th>
                                <th class="px-4 py-2 border">Marca</th>
                                <th class="px-4 py-2 border">Categoría</th>
                                <th class="px-4 py-2 border">Unidad</th>
                                <th class="px-4 py-2 border">Precio</th>
                                <th class="px-4 py-2 border text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos as $producto)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $producto->nombre }}</td>
                                    <td class="px-4 py-2 border">{{ $producto->marca }}</td>
                                    <td class="px-4 py-2 border">{{ $producto->categoria->nombre }}</td>
                                    <td class="px-4 py-2 border">{{ $producto->unidadMedida->nombre }}</td>
                                    <td class="px-4 py-2 border">${{ number_format($producto->precio, 2) }}</td>
                                    <td class="px-4 py-2 border text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('productos.edit', $producto) }}"
                                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-700">
                                                Editar
                                            </a>
                                            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar producto?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-700">
                                                    Eliminar
                                                </button>
                                            </form>
                                            <form action="{{ route('productos.toggle', $producto) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="px-3 py-1 text-white rounded-md {{ $producto->activo ? 'bg-gray-500 hover:bg-gray-700' : 'bg-green-500 hover:bg-green-700' }}">
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
