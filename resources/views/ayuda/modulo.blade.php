<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @php $mod = $modulos[$ayuda->modulo] ?? ['icono' => '📄', 'label' => $ayuda->modulo]; @endphp
                <span class="text-2xl">{{ $mod['icono'] }}</span>
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ $ayuda->titulo }}</h2>
                    <p class="text-sm text-gray-500">{{ $mod['label'] }} · {{ $ayuda->plataforma === 'movil' ? '📱 App Móvil' : '🌐 Panel Web' }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                @can('admin')
                <a href="{{ route('ayuda.edit', $ayuda) }}"
                   class="px-4 py-2 text-sm text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200">
                    ✏️ Editar
                </a>
                @endcan
                <a href="{{ route('ayuda.publico') }}"
                   class="px-4 py-2 text-sm bg-gray-100 rounded-lg hover:bg-gray-200">
                    ← Centro de ayuda
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl py-10 mx-auto space-y-6 sm:px-6 lg:px-8">

        {{-- Descripción corta --}}
        @if($ayuda->descripcion_corta)
            <div class="p-4 text-indigo-800 border border-indigo-200 bg-indigo-50 rounded-xl">
                💡 {{ $ayuda->descripcion_corta }}
            </div>
        @endif

        {{-- Pasos --}}
        @if(count($ayuda->pasos ?? []) > 0)
        <div class="p-6 bg-white shadow rounded-xl">
            <h3 class="mb-6 font-bold text-gray-700">Pasos a seguir</h3>
            <div class="space-y-6">
                @foreach($ayuda->pasos as $paso)
                <div class="flex gap-4">
                    {{-- Número --}}
                    <div class="flex flex-col items-center flex-shrink-0">
                        <div class="flex items-center justify-center text-sm font-bold text-white bg-indigo-600 rounded-full w-9 h-9">
                            {{ $paso['orden'] ?? $loop->iteration }}
                        </div>
                        @if(!$loop->last)
                            <div class="w-0.5 flex-1 bg-indigo-200 my-1 min-h-4"></div>
                        @endif
                    </div>
                    {{-- Contenido --}}
                    <div class="flex-1 pb-2">
                        <div class="flex items-center gap-2 mb-1">
                            @if(!empty($paso['icono']))
                                <span class="text-lg">{{ $paso['icono'] }}</span>
                            @endif
                            <h4 class="font-semibold text-gray-800">{{ $paso['titulo'] }}</h4>
                        </div>
                        @if(!empty($paso['descripcion']))
                            <p class="mb-3 text-sm leading-relaxed text-gray-600">{{ $paso['descripcion'] }}</p>
                        @endif

                        {{-- Imagen del paso --}}
                        @if(!empty($paso['imagen_url']))
                            <div class="mt-2">
                                <img src="{{ $paso['imagen_url'] }}"
                                     alt="Imagen paso {{ $paso['orden'] ?? $loop->iteration }}"
                                     class="object-contain transition border shadow-sm rounded-xl max-h-64 cursor-zoom-in hover:opacity-90"
                                     onclick="abrirLightbox('{{ $paso['imagen_url'] }}')">
                                <p class="mt-1 text-xs text-gray-400">Clic para ampliar</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Galería general --}}
        @if(count($ayuda->imagenes ?? []) > 0)
        <div class="p-6 bg-white shadow rounded-xl">
            <h3 class="mb-4 font-bold text-gray-700">Imágenes de referencia</h3>
            <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
                @foreach($ayuda->imagenes as $i => $img)
                <div class="relative overflow-hidden border shadow-sm cursor-zoom-in group rounded-xl"
                     onclick="abrirLightbox('{{ $img['url'] }}')">
                    <img src="{{ $img['url'] }}"
                         alt="Imagen {{ $i + 1 }}"
                         class="object-cover w-full h-40 transition duration-300 group-hover:scale-105">
                    <div class="absolute inset-0 flex items-center justify-center transition bg-black bg-opacity-0 group-hover:bg-opacity-20">
                        <span class="hidden text-2xl text-white group-hover:block">🔍</span>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="mt-3 text-xs text-gray-400">Clic en cualquier imagen para ampliarla.</p>
        </div>
        @endif

        {{-- Contenido adicional --}}
        @if($ayuda->contenido)
        <div class="p-6 bg-white shadow rounded-xl">
            <h3 class="mb-3 font-bold text-gray-700"> Notas adicionales</h3>
            <div class="leading-relaxed prose-sm prose text-gray-600 max-w-none">
                {!! $ayuda->contenido !!}
            </div>
        </div>
        @endif

    </div>

    {{-- Lightbox --}}
    <div id="lightbox"
         class="fixed inset-0 z-50 flex items-center justify-center hidden p-4 bg-black bg-opacity-90"
         onclick="cerrarLightbox()">
        <div class="relative w-full max-w-5xl" onclick="event.stopPropagation()">
            <button onclick="cerrarLightbox()"
                    class="absolute right-0 text-2xl font-bold text-white -top-10 hover:text-gray-300">
                ✕ Cerrar
            </button>
            <img id="lightbox-img" src="" alt="Imagen ampliada"
                 class="object-contain w-full max-h-screen shadow-2xl rounded-xl">
        </div>
    </div>

    @push('scripts')
    <script>
    function abrirLightbox(url) {
        document.getElementById('lightbox-img').src = url;
        document.getElementById('lightbox').classList.remove('hidden');
        document.getElementById('lightbox').classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function cerrarLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.getElementById('lightbox').classList.remove('flex');
        document.getElementById('lightbox-img').src = '';
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarLightbox(); });
    </script>
    @endpush

</x-app-layout>