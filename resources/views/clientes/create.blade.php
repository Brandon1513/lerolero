<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('clientes.index') }}"
                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">
                    Nuevo Cliente
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    Completa los datos del nuevo cliente
                </p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl py-8 mx-auto sm:px-6 lg:px-8">

        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 mb-5 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <ul class="list-disc list-inside space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('clientes.store') }}" class="space-y-4">
            @csrf

            {{-- Datos básicos --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-700">Datos generales</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nombre completo <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre', '') }}"
                            placeholder="Ej. Tienda La Esquina" required autofocus
                            class="w-full px-3 py-2 text-sm border rounded-lg outline-none transition focus:ring-2 focus:ring-indigo-500 {{ $errors->has('nombre') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"/>
                        @error('nombre')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', '') }}"
                            placeholder="Ej. 3324946170"
                            class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                        @error('telefono')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Asignado a vendedor</label>
                            <select name="asignado_a" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">— Sin asignar —</option>
                                @foreach($vendedores as $v)
                                    <option value="{{ $v->id }}" {{ old('asignado_a', '') == $v->id ? 'selected' : '' }}>{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nivel de precio</label>
                            <select name="nivel_precio_id" class="w-full px-3 py-2 text-sm transition bg-white border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">— Sin nivel —</option>
                                @foreach($niveles as $nivel)
                                    <option value="{{ $nivel->id }}" {{ old('nivel_precio_id', '') == $nivel->id ? 'selected' : '' }}>{{ $nivel->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dirección --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-700">Dirección</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Calle</label>
                            <input type="text" name="calle" value="{{ old('calle', '') }}"
                                placeholder="Ej. Granada #28"
                                class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Colonia</label>
                            <input type="text" name="colonia" value="{{ old('colonia', '') }}"
                                placeholder="Ej. Las Huertas"
                                class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Código Postal</label>
                            <input type="text" name="codigo_postal" value="{{ old('codigo_postal', '') }}"
                                placeholder="Ej. 45589"
                                class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Municipio</label>
                            <input type="text" name="municipio" value="{{ old('municipio', '') }}"
                                placeholder="Ej. Tlaquepaque"
                                class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Estado</label>
                            <input type="text" name="estado" value="{{ old('estado', '') }}"
                                placeholder="Ej. Jalisco"
                                class="w-full px-3 py-2 text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                        </div>
                    </div>

                    {{-- Mapa --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Ubicación en mapa</label>

                        {{-- Buscador de Google Places --}}
                        <div class="relative mb-2">
                            <svg class="absolute z-10 w-4 h-4 text-gray-400 -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input id="pac-input" type="text" placeholder="Buscar dirección, colonia, municipio..."
                                class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"/>
                        </div>

                        <div id="map" class="w-full overflow-hidden border border-gray-300 rounded-lg" style="height: 300px;"></div>

                        <button type="button" id="btnUbicacionActual"
                            class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Usar mi ubicación actual
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Latitud</label>
                            <input type="text" id="latitud" name="latitud" value="{{ old('latitud', '') }}"
                                placeholder="Ej. 20.6765"
                                class="w-full px-3 py-2 font-mono text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Longitud</label>
                            <input type="text" id="longitud" name="longitud" value="{{ old('longitud', '') }}"
                                placeholder="Ej. -103.3472"
                                class="w-full px-3 py-2 font-mono text-sm transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500"/>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Días de visita --}}
            <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100 bg-gray-50">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <h3 class="text-sm font-semibold text-gray-700">Días de visita</h3>
                </div>
                <div class="p-5">
                    @php
                        $diasSel = old('dias_visita', []);
                        if (is_string($diasSel)) $diasSel = explode(',', $diasSel);
                        $diasSel = array_map('trim', $diasSel);
                        $semana = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
                        $colores = ['indigo','blue','violet','purple','pink','orange','amber'];
                    @endphp
                    <div class="flex flex-wrap gap-2">
                        @foreach($semana as $i => $dia)
                            @php $checked = in_array($dia, $diasSel); $col = $colores[$i]; @endphp
                            <label class="cursor-pointer">
                                <input type="checkbox" name="dias_visita[]" value="{{ $dia }}"
                                    {{ $checked ? 'checked' : '' }} class="sr-only peer">
                                <div class="px-3 py-1.5 text-sm font-semibold rounded-lg border transition-all
                                    peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600
                                    {{ $checked ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:border-gray-400' }}">
                                    {{ $dia }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    Registrar Cliente
                </button>
                <a href="{{ route('clientes.index') }}"
                    class="flex-1 py-2.5 text-center bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

<script>
// ── Google Maps con Places Autocomplete ─────────────────────────────
let map, marker, autocomplete;

function initMap() {
    const initialLat = parseFloat(document.getElementById('latitud').value) || 20.6765;
    const initialLng = parseFloat(document.getElementById('longitud').value) || -103.3472;
    const initialPos = { lat: initialLat, lng: initialLng };

    // Crear mapa
    map = new google.maps.Map(document.getElementById('map'), {
        center: initialPos,
        zoom: 16,
        mapTypeControl: true,
        streetViewControl: true,
        fullscreenControl: true,
        mapTypeId: 'roadmap',
    });

    // Marcador arrastrable
    marker = new google.maps.Marker({
        position: initialPos,
        map: map,
        draggable: true,
        animation: google.maps.Animation.DROP,
        title: 'Arrastra para ajustar la ubicación',
    });

    // Al arrastrar el marcador
    marker.addListener('dragend', () => {
        const pos = marker.getPosition();
        setCoords(pos.lat(), pos.lng());
        reverseGeocode(pos.lat(), pos.lng());
    });

    // Al hacer click en el mapa
    map.addListener('click', (e) => {
        marker.setPosition(e.latLng);
        setCoords(e.latLng.lat(), e.latLng.lng());
        reverseGeocode(e.latLng.lat(), e.latLng.lng());
    });

    // ── Places Autocomplete ──────────────────────────────────────────
    const input = document.getElementById('pac-input');
    autocomplete = new google.maps.places.Autocomplete(input, {
        componentRestrictions: { country: 'mx' },  // Solo México
        fields: ['geometry', 'address_components', 'formatted_address'],
        types: ['geocode', 'establishment'],
    });

    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) return;

        const loc = place.geometry.location;
        map.setCenter(loc);
        map.setZoom(18);
        marker.setPosition(loc);
        setCoords(loc.lat(), loc.lng());

        // Autocompletar campos de dirección
        fillAddressFields(place.address_components);
    });
}

function setCoords(lat, lng) {
    document.getElementById('latitud').value = parseFloat(lat).toFixed(7);
    document.getElementById('longitud').value = parseFloat(lng).toFixed(7);
}

function fillAddressFields(components) {
    if (!components) return;
    const get = (type) => components.find(c => c.types.includes(type))?.long_name || '';

    const route         = get('route');
    const streetNum     = get('street_number');
    const colonia       = get('sublocality_level_1') || get('neighborhood') || get('sublocality');
    const municipio     = get('locality') || get('administrative_area_level_2');
    const estado        = get('administrative_area_level_1');
    const cp            = get('postal_code');

    if (route && !document.querySelector('[name=calle]').value)
        document.querySelector('[name=calle]').value = route + (streetNum ? ' #' + streetNum : '');
    if (colonia && !document.querySelector('[name=colonia]').value)
        document.querySelector('[name=colonia]').value = colonia;
    if (cp && !document.querySelector('[name=codigo_postal]').value)
        document.querySelector('[name=codigo_postal]').value = cp;
    if (municipio && !document.querySelector('[name=municipio]').value)
        document.querySelector('[name=municipio]').value = municipio;
    if (estado && !document.querySelector('[name=estado]').value)
        document.querySelector('[name=estado]').value = estado;
}

function reverseGeocode(lat, lng) {
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: { lat, lng }, region: 'MX' }, (results, status) => {
        if (status === 'OK' && results[0]) {
            fillAddressFields(results[0].address_components);
            // Actualizar el input de búsqueda con la dirección encontrada
            document.getElementById('pac-input').placeholder = results[0].formatted_address;
        }
    });
}

// ── Botón ubicación actual ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnUbicacionActual');
    btn.addEventListener('click', () => {
        if (!navigator.geolocation) {
            alert('Geolocalización no disponible en este navegador.');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg> Obteniendo ubicación...`;

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const loc = new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude);
                map.setCenter(loc);
                map.setZoom(18);
                marker.setPosition(loc);
                setCoords(pos.coords.latitude, pos.coords.longitude);
                reverseGeocode(pos.coords.latitude, pos.coords.longitude);

                btn.disabled = false;
                btn.innerHTML = `✅ Ubicación obtenida (precisión: ${Math.round(pos.coords.accuracy)}m)`;
                setTimeout(() => {
                    btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Usar mi ubicación actual`;
                }, 4000);
            },
            (err) => {
                btn.disabled = false;
                btn.innerHTML = `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Usar mi ubicación actual`;
                const msgs = {
                    1: 'Permiso denegado. Activa la ubicación en tu navegador.',
                    2: 'No se pudo obtener tu ubicación. Verifica el GPS.',
                    3: 'Tiempo agotado. Intenta de nuevo.',
                };
                alert(msgs[err.code] || 'No se pudo obtener la ubicación.');
            },
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
        );
    });
});
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB9z1pLnHycp5Vi2hU8RAsYlpzbfKHH4TE&libraries=places&callback=initMap&language=es&region=MX" async defer></script>