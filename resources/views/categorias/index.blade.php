<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Lista de Categorías') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('categorias.create') }}"
                            class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-700">
                            Agregar Categoría
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-200 divide-y divide-gray-200">
                            <thead class="bg-gray-100 text-left">
                                <tr>
                                    <th class="px-4 py-2 border">Nombre</th>
                                    <th class="px-4 py-2 border text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categorias as $categoria)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 border">{{ $categoria->nombre }}</td>
                                        <td class="px-4 py-2 border text-center">
                                            <div class="flex justify-center gap-2">
                                                <a href="{{ route('categorias.edit', $categoria) }}"
                                                    class="px-3 py-1 text-white bg-yellow-500 rounded hover:bg-yellow-700">
                                                    Editar
                                                </a>

                                                <form action="{{ route('categorias.destroy', $categoria) }}" method="POST"
                                                    onsubmit="return confirm('¿Eliminar categoría?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-3 py-1 text-white bg-red-500 rounded hover:bg-red-700">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-4 text-center text-gray-500">
                                            No hay categorías registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
