<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Historial de Producción</h2>
    </x-slot>

    <div class="max-w-6xl py-12 mx-auto sm:px-6 lg:px-8">

        <!-- Mensaje de éxito -->
        @if (session('success'))
            <div class="p-4 mb-4 text-green-700 bg-green-100 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Botón Crear -->
        <div class="mb-6">
            <a href="{{ route('producciones.create') }}"
               class="px-4 py-2 text-white bg-blue-500 rounded hover:bg-blue-700">
                Registrar Producción
            </a>
        </div>

        <!-- Tabla -->
        <div class="overflow-hidden bg-white rounded-lg shadow">
            <table class="w-full text-sm border border-collapse">
                <thead class="text-left bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">Fecha</th>
                        <th class="px-4 py-2 border">Producto</th>
                        <th class="px-4 py-2 border">Cantidad</th>
                        <th class="px-4 py-2 border">Lote</th>
                        <th class="px-4 py-2 border">Notas</th>
                        <th class="px-4 py-2 border">Registrado por</th>
                        <th class="px-4 py-2 text-center border">Acciones</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($producciones as $produccion)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 border">{{ $produccion->fecha }}</td>
                            <td class="px-4 py-2 border">{{ $produccion->producto->nombre }}</td>
                            <td class="px-4 py-2 border">{{ $produccion->cantidad }}</td>
                            <td class="px-4 py-2 border">{{ $produccion->lote ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $produccion->notas ?? '-' }}</td>
                            <td class="px-4 py-2 border">{{ $produccion->usuario->name }}</td>
                            <td class="px-4 py-2 text-center border">
                            <form action="{{ route('producciones.destroy', $produccion) }}" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta producción?')">
                                @csrf
                                @method('DELETE')
                                <x-danger-button class="text-xs">Eliminar</x-danger-button>
                            </form>
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                No hay producciones registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $producciones->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
