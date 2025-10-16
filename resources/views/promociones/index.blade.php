<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Promociones de Venta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Botón Agregar -->
                    <div class="mb-4">
                        <a href="{{ route('promociones.create') }}"
                           class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-700">
                            Agregar Promoción
                        </a>
                    </div>

                    <!-- Tabla -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border border-collapse border-gray-200">
                            <thead>
                                <tr class="text-left text-gray-700 bg-gray-100">
                                    <th class="px-4 py-2 border">Nombre</th>
                                    <th class="px-4 py-2 border">Descripción</th>
                                    <th class="px-4 py-2 border">Precio Promocional</th>
                                    <th class="px-4 py-2 border">Productos</th>
                                    <th class="px-4 py-2 border">Vigencia</th>
                                    <th class="px-4 py-2 text-center border">Estado</th>
                                    <th class="px-4 py-2 text-center border">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($promociones as $promo)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 font-semibold border">
                                            {{ $promo->nombre }}
                                        </td>

                                        <td class="px-4 py-2 border">
                                            {{ $promo->descripcion ?? 'Sin descripción' }}
                                        </td>

                                        <td class="px-4 py-2 border">
                                            ${{ number_format($promo->precio, 2) }}
                                        </td>

                                        <td class="px-4 py-2 border">
                                            @forelse($promo->productos as $producto)
                                                <div>
                                                    {{ $producto->nombre }}
                                                    <span class="text-sm text-gray-500">
                                                        (x{{ $producto->pivot->cantidad }})
                                                    </span>
                                                </div>
                                            @empty
                                                <span class="text-gray-400">Sin productos</span>
                                            @endforelse
                                        </td>

                                        <td class="px-4 py-2 border">
                                            <div class="flex flex-col">
                                                <span>
                                                    {{ $promo->fecha_inicio ? $promo->fecha_inicio->format('d/m/Y') : '—' }}
                                                    &nbsp;–&nbsp;
                                                    {{ $promo->fecha_fin ? $promo->fecha_fin->format('d/m/Y') : '—' }}
                                                </span>
                                                {{-- chip de vigencia --}}
                                                @php
                                                    $vig = $promo->vigencia_estado; // proxima | vigente | expirada
                                                    $colors = [
                                                        'proxima'  => 'bg-blue-100 text-blue-700',
                                                        'vigente'  => 'bg-green-100 text-green-700',
                                                        'expirada' => 'bg-red-100 text-red-700',
                                                    ];
                                                @endphp
                                                <span class="mt-1 inline-block px-2 py-0.5 text-xs rounded {{ $colors[$vig] }}">
                                                    {{ $promo->vigencia_label }}
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-4 py-2 text-center border">
                                            @if ($promo->activo)
                                                <span class="inline-block px-2 py-1 mb-1 mr-1 text-xs text-white bg-green-500 rounded">Activo</span>
                                            @else
                                                <span class="inline-block px-2 py-1 mb-1 mr-1 text-xs text-white bg-gray-500 rounded">Inactivo</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-2 text-center border">
                                            <div class="flex flex-col items-center gap-2">
                                                <!-- Activar/Inactivar -->
                                                <form action="{{ route('promociones.toggle', $promo) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <button type="submit"
                                                            class="px-3 py-1 text-white rounded-md
                                                            {{ $promo->activo ? 'bg-gray-500 hover:bg-gray-700' : 'bg-green-500 hover:bg-green-700' }}">
                                                        {{ $promo->activo ? 'Inactivar' : 'Activar' }}
                                                    </button>
                                                </form>

                                                <div class="flex flex-wrap justify-center gap-2">
                                                    <a href="{{ route('promociones.edit', $promo) }}"
                                                       class="px-3 py-1 text-white bg-yellow-500 rounded-md hover:bg-yellow-700">
                                                        Editar
                                                    </a>

                                                    <form action="{{ route('promociones.destroy', $promo) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Eliminar esta promoción?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                                class="px-3 py-1 text-white bg-red-500 rounded-md hover:bg-red-700">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-4">
                            {{ $promociones->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
