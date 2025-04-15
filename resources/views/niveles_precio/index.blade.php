<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Lista de Niveles de Precio') }}
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
                    <a href="{{ route('niveles-precio.create') }}"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">
                        Agregar Nivel de Precio
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border border-collapse border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-100 text-left">
                            <tr>
                                <th class="px-4 py-2 border">Nombre</th>
                                <th class="px-4 py-2 border text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($niveles as $nivel)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 border">{{ $nivel->nombre }}</td>
                                    <td class="px-4 py-2 border text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('niveles-precio.edit', $nivel) }}"
                                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-700">
                                                Editar
                                            </a>
                                            <form action="{{ route('niveles-precio.destroy', $nivel) }}" method="POST"
                                                onsubmit="return confirm('¿Eliminar este nivel de precio?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-700">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-4 text-center text-gray-500">
                                        No hay niveles de precio registrados.
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
