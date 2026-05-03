<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">📚 Gestión de Ayuda</h2>
            <div class="flex gap-3">
                <a href="{{ route('ayuda.publico') }}" target="_blank"
                   class="px-4 py-2 text-sm bg-gray-100 rounded hover:bg-gray-200">
                    👁 Ver como usuario
                </a>
                <a href="{{ route('ayuda.create') }}"
                   class="px-4 py-2 text-sm text-white bg-indigo-600 rounded hover:bg-indigo-700">
                    + Nueva guía
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 mx-auto max-w-7xl sm:px-6 lg:px-8">

        @if(session('success'))
            <div class="p-4 mb-6 text-green-800 bg-green-100 rounded-lg">{{ session('success') }}</div>
        @endif

        <div class="overflow-hidden bg-white rounded-lg shadow">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-left text-gray-600">Módulo</th>
                        <th class="px-4 py-3 font-semibold text-left text-gray-600">Título</th>
                        <th class="px-4 py-3 font-semibold text-center text-gray-600">Plataforma</th>
                        <th class="px-4 py-3 font-semibold text-center text-gray-600">Pasos</th>
                        <th class="px-4 py-3 font-semibold text-center text-gray-600">Activo</th>
                        <th class="px-4 py-3 font-semibold text-center text-gray-600">Orden</th>
                        <th class="px-4 py-3 font-semibold text-center text-gray-600">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ayudas as $ayuda)
                        @php $mod = $modulos[$ayuda->modulo] ?? null; @endphp
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="font-medium">
                                    {{ $mod['icono'] ?? '📄' }} {{ $mod['label'] ?? $ayuda->modulo }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $ayuda->titulo }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full font-semibold
                                    {{ $ayuda->plataforma === 'web' ? 'bg-blue-100 text-blue-700' :
                                       ($ayuda->plataforma === 'movil' ? 'bg-green-100 text-green-700' :
                                       'bg-purple-100 text-purple-700') }}">
                                    {{ $ayuda->plataforma === 'ambas' ? '🌐📱 Ambas' :
                                       ($ayuda->plataforma === 'web' ? '🌐 Web' : '📱 Móvil') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold text-indigo-600">{{ count($ayuda->pasos ?? []) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($ayuda->activo)
                                    <span class="font-bold text-green-600">✅</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $ayuda->orden }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('ayuda.modulo', [$ayuda->modulo, $ayuda->plataforma]) }}"
                                       target="_blank"
                                       class="px-3 py-1 text-xs bg-gray-100 rounded hover:bg-gray-200">
                                        Ver
                                    </a>
                                    <a href="{{ route('ayuda.edit', $ayuda) }}"
                                       class="px-3 py-1 text-xs text-indigo-700 bg-indigo-100 rounded hover:bg-indigo-200">
                                        Editar
                                    </a>
                                    <form method="POST" action="{{ route('ayuda.destroy', $ayuda) }}"
                                          onsubmit="return confirm('¿Eliminar esta guía?')">
                                        @csrf @method('DELETE')
                                        <button class="px-3 py-1 text-xs text-red-700 bg-red-100 rounded hover:bg-red-200">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                No hay guías creadas aún.
                                <a href="{{ route('ayuda.create') }}" class="ml-2 text-indigo-600 underline">Crear la primera</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>