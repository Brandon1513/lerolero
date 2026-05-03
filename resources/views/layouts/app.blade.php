<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                <div class="px-4 mx-auto mt-4 max-w-7xl sm:px-6 lg:px-8">
                    {{-- ── ALERTAS ── --}}
                    @if(session('success'))
                        <div class="flex items-center gap-3 px-4 py-3 mb-4 text-sm text-green-800 border border-green-200 bg-green-50 rounded-xl">
                            <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="flex items-center gap-3 px-4 py-3 mb-4 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                            <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9v4a1 1 0 102 0V9a1 1 0 10-2 0zm0-4a1 1 0 112 0 1 1 0 01-2 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif
                </div>

                {{ $slot }}
            </main>
        </div>

        {{-- ── BOTÓN FLOTANTE DE AYUDA ── --}}
        {{-- Detecta el módulo por la URL y muestra el botón solo si existe una guía --}}
        @php
            $segmento = request()->segment(1); // producciones, inventario, cierres, etc.
            $ayudaFlotante = null;
            if ($segmento) {
                $ayudaFlotante = \App\Models\Ayuda::where('modulo', $segmento)
                    ->where('activo', true)
                    ->whereIn('plataforma', ['web', 'ambas'])
                    ->first();
            }
        @endphp

        @if($ayudaFlotante)
        <div class="fixed z-50 bottom-6 right-6">
            <a href="{{ route('ayuda.modulo', [$ayudaFlotante->modulo, 'web']) }}"
               target="_blank"
               rel="noopener noreferrer"
               class="flex items-center gap-2 px-4 py-3 text-sm font-semibold text-white transition-all bg-indigo-600 rounded-full shadow-lg hover:bg-indigo-700 hover:shadow-xl group">
                <span class="flex items-center justify-center w-5 h-5 font-bold text-indigo-600 bg-white rounded-full">?</span>
                <span>Ayuda</span>
                {{-- Tooltip con título --}}
                @if($ayudaFlotante->descripcion_corta)
                <span class="absolute right-0 hidden px-3 py-2 mb-2 text-xs text-white bg-gray-900 rounded-lg shadow-lg pointer-events-none group-hover:block bottom-full w-52">
                    {{ $ayudaFlotante->descripcion_corta }}
                    <span class="absolute w-2 h-2 rotate-45 bg-gray-900 -bottom-1 right-6"></span>
                </span>
                @endif
            </a>
        </div>
        @endif

        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
        @stack('scripts')
    </body>
</html>