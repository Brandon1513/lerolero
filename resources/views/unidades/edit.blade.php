<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('unidades.index') }}"
                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Editar Unidad de Medida</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $unidad->nombre }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-lg py-8 mx-auto sm:px-6 lg:px-8">

        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 mb-6 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <div><ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            </div>
        @endif

        <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Editar datos
                </h3>
                @if($unidad->activo)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Activa
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactiva
                    </span>
                @endif
            </div>
            <div class="p-5">
                <form method="POST" action="{{ route('unidades.update', $unidad) }}" class="space-y-4">
                    @csrf @method('PUT')

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input id="nombre" name="nombre" type="text" required autofocus
                            value="{{ old('nombre', $unidad->nombre) }}"
                            class="w-full px-3 py-2.5 text-sm border rounded-lg outline-none transition
                                {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500' }}"/>
                        @error('nombre')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="equivalente" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Equivalente <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute text-sm font-semibold text-gray-500 -translate-y-1/2 left-3 top-1/2">×</span>
                            <input id="equivalente" name="equivalente" type="number" min="1" step="1" required
                                value="{{ old('equivalente', $unidad->equivalente) }}"
                                class="w-full pl-7 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition font-semibold"/>
                        </div>
                        @error('equivalente')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex gap-2 pt-2">
                        <button type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Guardar Cambios
                        </button>
                        <a href="{{ route('unidades.index') }}"
                            class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-white text-gray-700 text-sm font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>