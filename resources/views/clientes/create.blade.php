<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Agregar Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <h2 class="mb-6 text-lg font-bold text-gray-700">
                        {{ __('Nuevo Cliente') }}
                    </h2>

                    <form method="POST" action="{{ route('clientes.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="nombre" :value="__('Nombre Completo')" />
                            <x-text-input id="nombre" name="nombre" type="text" aria-placeholder="hola mundo" class="block w-full mt-1" :value="old('nombre')" placeholder="Ej. Juan Pérez" required autofocus />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="telefono" :value="__('Teléfono')" />
                            <x-text-input id="telefono" name="telefono" type="text" class="block w-full mt-1" :value="old('telefono')" placeholder="Ej. 3324946170" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="calle" :value="__('Calle')" />
                            <x-text-input id="calle" name="calle" type="text" class="block w-full mt-1" :value="old('calle')" placeholder="Ej. Ganada #28" />
                            <x-input-error :messages="$errors->get('calle')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="colonia" :value="__('Colonia')" />
                            <x-text-input id="colonia" name="colonia" type="text" class="block w-full mt-1" :value="old('colonia')" placeholder="Ej. Las Huertas" />
                            <x-input-error :messages="$errors->get('colonia')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="codigo_postal" :value="__('Código Postal')" />
                            <x-text-input id="codigo_postal" name="codigo_postal" type="text" class="block w-full mt-1" :value="old('codigo_postal')" placeholder="Ej. 45589" />
                            <x-input-error :messages="$errors->get('codigo_postal')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="municipio" :value="__('Municipio')" />
                            <x-text-input id="municipio" name="municipio" type="text" class="block w-full mt-1" :value="old('municipio')" placeholder="Ej. Tlaquepaque" />
                            <x-input-error :messages="$errors->get('municipio')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="estado" :value="__('Estado')" />
                            <x-text-input id="estado" name="estado" type="text" class="block w-full mt-1" :value="old('estado')" placeholder="Ej. Jalisco" />
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="asignado_a" :value="__('Asignar a Vendedor')" />
                            <select name="asignado_a" id="asignado_a" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                <option value="">-- Seleccione --</option>
                                @foreach ($vendedores as $vendedor)
                                    <option value="{{ $vendedor->id }}" {{ old('asignado_a') == $vendedor->id ? 'selected' : '' }}>
                                        {{ $vendedor->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('asignado_a')" class="mt-2" />
                        </div>
                        
                        <div class="mb-4">
                            <x-input-label for="nivel_precio_id" value="Nivel de Precio" />
                            <select name="nivel_precio_id" id="nivel_precio_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Selecciona un nivel --</option>
                                @foreach ($niveles as $nivel)
                                    <option value="{{ $nivel->id }}" {{ old('nivel_precio_id', $cliente->nivel_precio_id ?? '') == $nivel->id ? 'selected' : '' }}>
                                        {{ $nivel->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('nivel_precio_id')" class="mt-2" />
                        </div>
                        <div id="map" style="height: 300px;" class="my-4 rounded shadow"></div>

                        <div class="mb-4">
                            <x-input-label for="latitud" :value="__('Latitud')" />
                            <x-text-input id="latitud" name="latitud" type="text" class="block w-full mt-1" :value="old('latitud')" placeholder="Ej. 20.6765" />
                            <x-input-error :messages="$errors->get('latitud')" class="mt-2" />
                        </div>
                        
                        <div class="mb-4">
                            <x-input-label for="longitud" :value="__('Longitud')" />
                            <x-text-input id="longitud" name="longitud" type="text" class="block w-full mt-1" :value="old('longitud')" placeholder="Ej. -103.3472" />
                            <x-input-error :messages="$errors->get('longitud')" class="mt-2" />
                        </div>
                        
                        <div class="mb-4">
                            <x-input-label for="dias_visita" :value="__('Días de Visita')" />
                            <div class="flex flex-wrap gap-4 mt-2">
                                @foreach (['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'] as $dia)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="dias_visita[]" value="{{ $dia }}"
                                            {{ in_array($dia, old('dias_visita', $cliente->dias_visita ?? [])) ? 'checked' : '' }}
                                            class="text-indigo-600 border-gray-300 rounded shadow-sm focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $dia }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('dias_visita')" class="mt-2" />
                        </div>
                        
                        

                        <div class="flex justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Guardar Cliente') }}
                            </x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const map = L.map('map').setView([20.6765, -103.3472], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker;

        map.on('click', function (e) {
            const lat = e.latlng.lat.toFixed(7);
            const lng = e.latlng.lng.toFixed(7);

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng).addTo(map);
            }

            document.getElementById('latitud').value = lat;
            document.getElementById('longitud').value = lng;
        });

        // ✅ Agrega el buscador de direcciones
        const geocoder = L.Control.geocoder({
            defaultMarkGeocode: false,
        })
        .on('markgeocode', function(e) {
            const latlng = e.geocode.center;
            map.setView(latlng, 17);

            if (marker) {
                marker.setLatLng(latlng);
            } else {
                marker = L.marker(latlng).addTo(map);
            }

            document.getElementById('latitud').value = latlng.lat.toFixed(7);
            document.getElementById('longitud').value = latlng.lng.toFixed(7);
        })
        .addTo(map);
    });
</script>
