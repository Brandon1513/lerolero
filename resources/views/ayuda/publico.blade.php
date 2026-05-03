<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">📚 Centro de Ayuda</h2>
    </x-slot>

    <div class="py-10 mx-auto space-y-10 max-w-7xl sm:px-6 lg:px-8">

        {{-- WEB --}}
        <div>
            <h3 class="mb-4 text-lg font-bold text-gray-700">🌐 Guías del Panel Web</h3>
            @if($web->isEmpty())
                <p class="text-sm text-gray-400">No hay guías disponibles aún.</p>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($web as $ayuda)
                        @php $mod = $modulos[$ayuda->modulo] ?? ['icono' => '📄', 'label' => $ayuda->modulo]; @endphp
                        <a href="{{ route('ayuda.modulo', [$ayuda->modulo, $ayuda->plataforma === 'ambas' ? 'web' : $ayuda->plataforma]) }}"
                           class="block p-5 transition bg-white border border-transparent shadow rounded-xl hover:shadow-md hover:border-indigo-200">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-2xl">{{ $mod['icono'] }}</span>
                                <div>
                                    <div class="font-bold text-gray-800">{{ $ayuda->titulo }}</div>
                                    <div class="text-xs text-gray-500">{{ $mod['label'] }}</div>
                                </div>
                            </div>
                            @if($ayuda->descripcion_corta)
                                <p class="mt-2 text-sm text-gray-600">{{ $ayuda->descripcion_corta }}</p>
                            @endif
                            @if(count($ayuda->pasos ?? []) > 0)
                                <div class="mt-3 text-xs font-semibold text-indigo-600">
                                    {{ count($ayuda->pasos) }} pasos →
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- MÓVIL --}}
        <div>
            <h3 class="mb-2 text-lg font-bold text-gray-700">📱 Guías para Vendedores (App Móvil)</h3>
            <p class="mb-4 text-sm text-gray-500">Comparte estas guías con tus vendedores para que aprendan a usar la aplicación.</p>
            @if($movil->isEmpty())
                <p class="text-sm text-gray-400">No hay guías para la app móvil aún.</p>
            @else
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($movil as $ayuda)
                        @php $mod = $modulos[$ayuda->modulo] ?? ['icono' => '📄', 'label' => $ayuda->modulo]; @endphp
                        <a href="{{ route('ayuda.modulo', [$ayuda->modulo, 'movil']) }}"
                           class="block p-5 transition bg-white border border-transparent shadow rounded-xl hover:shadow-md hover:border-green-200">
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-2xl">{{ $mod['icono'] }}</span>
                                <div>
                                    <div class="font-bold text-gray-800">{{ $ayuda->titulo }}</div>
                                    <div class="text-xs text-gray-500">{{ $mod['label'] }}</div>
                                </div>
                            </div>
                            @if($ayuda->descripcion_corta)
                                <p class="mt-2 text-sm text-gray-600">{{ $ayuda->descripcion_corta }}</p>
                            @endif
                            @if(count($ayuda->pasos ?? []) > 0)
                                <div class="mt-3 text-xs font-semibold text-green-600">
                                    {{ count($ayuda->pasos) }} pasos →
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

    </div>
</x-app-layout>