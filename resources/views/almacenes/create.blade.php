<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('almacenes.index') }}"
                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Nuevo Almacén</h2>
                <p class="text-sm text-gray-500 mt-0.5">Define nombre, tipo y usuario asignado</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl py-8 mx-auto sm:px-6 lg:px-8">

        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 mb-6 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <div><ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            </div>
        @endif

        <form method="POST" action="{{ route('almacenes.store') }}">
            @csrf
            <div class="space-y-5">

                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/></svg>
                            Información del almacén
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre <span class="text-red-500">*</span></label>
                            <input id="nombre" name="nombre" type="text" required autofocus
                                value="{{ old('nombre') }}" placeholder="Ej. Almacén Central, Almacén Juan..."
                                class="w-full px-3 py-2.5 text-sm border rounded-lg outline-none transition {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-gray-300 focus:ring-2 focus:ring-indigo-500' }}"/>
                            @error('nombre')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1.5">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="2" placeholder="Descripción opcional..."
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition resize-none">{{ old('descripcion') }}</textarea>
                        </div>
                        <div>
                            <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-1.5">Ubicación</label>
                            <input id="ubicacion" name="ubicacion" type="text" value="{{ old('ubicacion') }}"
                                placeholder="Ej. Calle Principal #10, Bodega B..."
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition"/>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                        <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                            Tipo y asignación
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        @php $tipoActual = old('tipo', 'general'); @endphp
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de almacén <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach(['general' => ['General','M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'], 'vendedor' => ['Vendedor','M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'], 'rechazo' => ['Rechazo','M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636']] as $val => $cfg)
                                    <label class="tipo-card cursor-pointer rounded-lg border-2 p-3 flex flex-col items-center gap-1.5 transition-all {{ $tipoActual === $val ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                        <input type="radio" name="tipo" value="{{ $val }}" class="sr-only" {{ $tipoActual === $val ? 'checked' : '' }}>
                                        <svg class="w-5 h-5 {{ $tipoActual === $val ? 'text-indigo-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $cfg[1] }}"/></svg>
                                        <span class="text-xs font-semibold {{ $tipoActual === $val ? 'text-indigo-700' : 'text-gray-600' }}">{{ $cfg[0] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div id="vendedor-field" class="{{ $tipoActual !== 'vendedor' ? 'hidden' : '' }}">
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1.5">Vendedor asignado</label>
                            <select name="user_id" id="user_id"
                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="">— Selecciona un vendedor —</option>
                                @foreach($usuarios as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-3 pt-1">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="activo" value="1" checked class="sr-only peer">
                                <div class="w-10 h-6 bg-gray-200 peer-checked:bg-indigo-600 rounded-full transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-4"></div>
                            </label>
                            <span class="text-sm font-medium text-gray-700">Activo al crear</span>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Crear Almacén
                    </button>
                    <a href="{{ route('almacenes.index') }}"
                        class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-white text-gray-700 text-sm font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
    document.querySelectorAll('.tipo-card').forEach(card => {
        card.addEventListener('click', () => {
            const radio = card.querySelector('input[type=radio]');
            radio.checked = true;
            const val = radio.value;
            document.querySelectorAll('.tipo-card').forEach(c => {
                c.classList.remove('border-indigo-500','bg-indigo-50');
                c.classList.add('border-gray-200','bg-white');
                c.querySelector('svg').classList.replace('text-indigo-600','text-gray-400');
                c.querySelector('span').classList.replace('text-indigo-700','text-gray-600');
            });
            card.classList.add('border-indigo-500','bg-indigo-50');
            card.classList.remove('border-gray-200','bg-white');
            card.querySelector('svg').classList.replace('text-gray-400','text-indigo-600');
            card.querySelector('span').classList.replace('text-gray-600','text-indigo-700');
            document.getElementById('vendedor-field').classList.toggle('hidden', val !== 'vendedor');
            if (val !== 'vendedor') document.getElementById('user_id').value = '';
        });
    });
    </script>
</x-app-layout>