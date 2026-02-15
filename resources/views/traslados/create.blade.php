<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('traslados.index') }}"
                class="inline-flex items-center justify-center w-8 h-8 text-gray-600 transition-colors bg-gray-100 rounded-lg hover:bg-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold tracking-tight text-gray-900">Nuevo Traslado</h2>
                <p class="text-sm text-gray-500 mt-0.5">Mueve inventario entre almacenes</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-5xl py-8 mx-auto sm:px-6 lg:px-8">

        {{-- Errores --}}
        @if($errors->any())
            <div class="flex items-start gap-3 px-4 py-3 mb-6 text-sm text-red-800 border border-red-200 bg-red-50 rounded-xl">
                <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <div class="mb-1 font-semibold">Hay errores en el formulario:</div>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('traslados.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- ── COLUMNA IZQUIERDA: Configuración ── --}}
                <div class="space-y-5">

                    {{-- Card: Almacenes --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Ruta del traslado
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">

                            {{-- Origen --}}
                            <div>
                                <label for="almacen_origen_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Almacén Origen <span class="text-red-500">*</span>
                                </label>
                                <select name="almacen_origen_id" id="almacen_origen_id" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                                    <option value="">— Selecciona —</option>
                                    @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}" data-tipo="{{ $almacen->tipo ?? '' }}">
                                            {{ $almacen->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Flecha visual --}}
                            <div class="flex justify-center">
                                <div class="flex flex-col items-center gap-1 text-gray-300">
                                    <div class="w-px h-3 bg-gray-200"></div>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    <div class="w-px h-3 bg-gray-200"></div>
                                </div>
                            </div>

                            {{-- Destino --}}
                            <div>
                                <label for="almacen_destino_id" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Almacén Destino <span class="text-red-500">*</span>
                                </label>
                                <select name="almacen_destino_id" id="almacen_destino_id" required
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                                    <option value="">— Selecciona —</option>
                                    @foreach($almacenes as $almacen)
                                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Card: Fecha y observaciones --}}
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Detalles
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div>
                                <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Fecha <span class="text-red-500">*</span>
                                </label>
                                <input name="fecha" id="fecha" type="date" required
                                    value="{{ now()->toDateString() }}"
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"/>
                            </div>
                            <div>
                                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1.5">
                                    Observaciones
                                </label>
                                <textarea name="observaciones" id="observaciones" rows="3"
                                    placeholder="Notas opcionales..."
                                    class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Botones --}}
                    <div class="flex flex-col gap-2">
                        <button type="submit" id="btn-submit" disabled
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Registrar Traslado
                        </button>
                        <a href="{{ route('traslados.index') }}"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white text-gray-700 text-sm font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </a>
                    </div>

                </div>

                {{-- ── COLUMNA DERECHA: Productos ── --}}
                <div class="lg:col-span-2">
                    <div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
                        {{-- Header principal --}}
                        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
                            <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                                Productos a trasladar
                            </h3>
                            <span id="contador-total" class="hidden text-xs font-semibold bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full">
                                0 unidades seleccionadas
                            </span>
                        </div>

                        {{-- Filtro de categorías (se llena con JS) --}}
                        <div id="filtro-categorias" class="hidden px-5 py-3 bg-white border-b border-gray-100">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="mr-1 text-xs font-semibold tracking-wide text-gray-500 uppercase">Filtrar:</span>
                                <button type="button"
                                    data-cat="todas"
                                    onclick="filtrarCategoria('todas', this)"
                                    class="cat-pill inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full transition-all border
                                        bg-indigo-600 text-white border-indigo-600 shadow-sm">
                                    Todas
                                </button>
                                {{-- Las demás pills se generan en JS --}}
                            </div>
                        </div>

                        {{-- Estado inicial --}}
                        <div id="estado-inicial" class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                            <div class="flex items-center justify-center bg-gray-100 rounded-full w-14 h-14">
                                <svg class="text-gray-400 w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500">Selecciona un almacén origen<br>para ver los productos disponibles.</p>
                        </div>

                        {{-- Loading --}}
                        <div id="estado-cargando" class="flex flex-col items-center justify-center hidden gap-3 py-16">
                            <div class="w-8 h-8 border-2 border-indigo-500 rounded-full border-t-transparent animate-spin"></div>
                            <p class="text-sm text-gray-500">Cargando inventario...</p>
                        </div>

                        {{-- Sin productos --}}
                        <div id="estado-vacio" class="flex flex-col items-center justify-center hidden gap-3 py-16">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                            <p class="text-sm text-gray-500">No hay productos disponibles en este almacén.</p>
                        </div>

                        {{-- Contenedor de categorías --}}
                        <div id="contenedor-productos" class="hidden divide-y divide-gray-100">
                            {{-- Se llena con JS --}}
                        </div>

                    </div>
                </div>

            </div>
        </form>
    </div>

<script>
// Colores por categoría (se asignan dinámicamente)
const CATEGORIA_COLORES = [
    { bg: 'bg-purple-100',  text: 'text-purple-700',  dot: 'bg-purple-500'  },
    { bg: 'bg-blue-100',    text: 'text-blue-700',    dot: 'bg-blue-500'    },
    { bg: 'bg-green-100',   text: 'text-green-700',   dot: 'bg-green-500'   },
    { bg: 'bg-orange-100',  text: 'text-orange-700',  dot: 'bg-orange-500'  },
    { bg: 'bg-pink-100',    text: 'text-pink-700',    dot: 'bg-pink-500'    },
    { bg: 'bg-teal-100',    text: 'text-teal-700',    dot: 'bg-teal-500'    },
    { bg: 'bg-amber-100',   text: 'text-amber-700',   dot: 'bg-amber-500'   },
    { bg: 'bg-red-100',     text: 'text-red-700',     dot: 'bg-red-500'     },
];

let totalUnidades = 0;

function show(id) {
    ['estado-inicial','estado-cargando','estado-vacio','contenedor-productos']
        .forEach(i => document.getElementById(i).classList.add('hidden'));
    document.getElementById(id).classList.remove('hidden');

    // Mostrar filtro de categorías solo cuando hay productos
    const filtro = document.getElementById('filtro-categorias');
    if (id === 'contenedor-productos') {
        filtro.classList.remove('hidden');
    } else {
        filtro.classList.add('hidden');
    }
}

// Colores de pill por categoría (mismo orden que CATEGORIA_COLORES)
const PILL_INACTIVO = [
    'bg-purple-100 text-purple-700 border-purple-200',
    'bg-blue-100 text-blue-700 border-blue-200',
    'bg-green-100 text-green-700 border-green-200',
    'bg-orange-100 text-orange-700 border-orange-200',
    'bg-pink-100 text-pink-700 border-pink-200',
    'bg-teal-100 text-teal-700 border-teal-200',
    'bg-amber-100 text-amber-700 border-amber-200',
    'bg-red-100 text-red-700 border-red-200',
];
const PILL_ACTIVO = [
    'bg-purple-600 text-white border-purple-600',
    'bg-blue-600 text-white border-blue-600',
    'bg-green-600 text-white border-green-600',
    'bg-orange-600 text-white border-orange-600',
    'bg-pink-600 text-white border-pink-600',
    'bg-teal-600 text-white border-teal-600',
    'bg-amber-600 text-white border-amber-600',
    'bg-red-600 text-white border-red-600',
];

// Mapa: nombre de categoría → índice de color
const catColorIdx = {};

function filtrarCategoria(cat, btnClicked) {
    // Actualizar pills
    document.querySelectorAll('.cat-pill').forEach(btn => {
        const c = btn.dataset.cat;
        const idx = catColorIdx[c];

        if (btn === btnClicked) {
            // Activar esta pill
            btn.className = btn.className
                .replace(/bg-\S+ text-\S+ border-\S+ shadow-sm|bg-indigo-600 text-white border-indigo-600 shadow-sm/g, '')
                .trim();
            if (c === 'todas') {
                btn.className += ' bg-indigo-600 text-white border-indigo-600 shadow-sm';
            } else {
                btn.className += ' ' + PILL_ACTIVO[idx] + ' shadow-sm';
            }
        } else {
            // Desactivar
            btn.className = btn.className
                .replace(/bg-\S+ text-\S+ border-\S+ shadow-sm|bg-indigo-600 text-white border-indigo-600 shadow-sm/g, '')
                .trim();
            if (c === 'todas') {
                btn.className += ' bg-white text-gray-600 border-gray-200 hover:bg-gray-50';
            } else {
                btn.className += ' ' + PILL_INACTIVO[idx];
            }
        }
    });

    // Mostrar/ocultar secciones de categoría
    const contenedor = document.getElementById('contenedor-productos');
    // Cada par header+body tiene data-categoria
    contenedor.querySelectorAll('[data-categoria]').forEach(el => {
        if (cat === 'todas' || el.dataset.categoria === cat) {
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    });
}

function generarPillsCategorias(categorias) {
    const wrap = document.querySelector('#filtro-categorias .flex');
    // Quitar pills anteriores (mantener "Todas")
    wrap.querySelectorAll('.cat-pill:not([data-cat="todas"])').forEach(p => p.remove());

    // Resetear "Todas" a activo
    const btnTodas = wrap.querySelector('[data-cat="todas"]');
    btnTodas.className = 'cat-pill inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full transition-all border bg-indigo-600 text-white border-indigo-600 shadow-sm';

    categorias.forEach((cat, idx) => {
        catColorIdx[cat] = idx % PILL_INACTIVO.length;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.dataset.cat = cat;
        btn.dataset.categoria = cat;
        btn.className = `cat-pill inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-full transition-all border ${PILL_INACTIVO[idx % PILL_INACTIVO.length]}`;
        btn.textContent = cat;
        btn.addEventListener('click', () => filtrarCategoria(cat, btn));
        wrap.appendChild(btn);
    });
}

function actualizarContador() {
    totalUnidades = 0;
    document.querySelectorAll('input[name*="[cantidad]"]').forEach(input => {
        const v = parseFloat(input.value) || 0;
        if (v > 0) totalUnidades += v;
    });

    const badge = document.getElementById('contador-total');
    const btn   = document.getElementById('btn-submit');

    if (totalUnidades > 0) {
        badge.textContent = `${totalUnidades} unidades seleccionadas`;
        badge.classList.remove('hidden');
        btn.disabled = false;
    } else {
        badge.classList.add('hidden');
        btn.disabled = true;
    }
}

function badgeCaducidad(fecha) {
    if (!fecha) return '<span class="text-xs text-gray-400">—</span>';
    const hoy   = new Date();
    const cad   = new Date(fecha + 'T00:00:00');
    const dias  = Math.floor((cad - hoy) / 86400000);
    let cls, txt;
    if (dias < 0) {
        cls = 'bg-red-100 text-red-700';
        txt = `Vencido`;
    } else if (dias <= 30) {
        cls = 'bg-amber-100 text-amber-700';
        txt = `Vence en ${dias}d`;
    } else {
        cls = 'bg-green-100 text-green-700';
        txt = fecha.split('-').reverse().join('/');
    }
    return `<span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full ${cls}">${txt}</span>`;
}

document.getElementById('almacen_origen_id').addEventListener('change', function () {
    const almacenId = this.value;
    if (!almacenId) {
        show('estado-inicial');
        document.getElementById('filtro-categorias').classList.add('hidden');
        return;
    }

    show('estado-cargando');

    fetch(`/traslados/lotes/${almacenId}`)
        .then(r => r.json())
        .then(data => {
            const contenedor = document.getElementById('contenedor-productos');
            contenedor.innerHTML = '';

            if (Object.keys(data).length === 0) { show('estado-vacio'); return; }

            // Agrupar productos por categoría
            const porCategoria = {};
            Object.entries(data).forEach(([productoId, lotes]) => {
                const cat = lotes[0]?.categoria ?? 'Sin categoría';
                if (!porCategoria[cat]) porCategoria[cat] = [];
                porCategoria[cat].push({ productoId, lotes });
            });

            let colorIdx = 0;

            Object.entries(porCategoria).forEach(([categoria, productos]) => {
                const color = CATEGORIA_COLORES[colorIdx % CATEGORIA_COLORES.length];
                colorIdx++;

                // Encabezado de categoría (colapsable)
                const catId = `cat-${categoria.replace(/\s+/g, '-').toLowerCase()}`;

                const catHeader = document.createElement('div');
                catHeader.className = 'px-5 py-3 cursor-pointer select-none flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors';
                catHeader.setAttribute('data-target', catId);
                catHeader.setAttribute('data-categoria', categoria);
                catHeader.innerHTML = `
                    <div class="flex items-center gap-2.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full ${color.bg} ${color.text}">
                            <span class="w-1.5 h-1.5 rounded-full ${color.dot}"></span>
                            ${categoria}
                        </span>
                        <span class="text-xs text-gray-400">${productos.length} producto${productos.length !== 1 ? 's' : ''}</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400 transition-transform cat-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                `;

                const catBody = document.createElement('div');
                catBody.id = catId;
                catBody.className = 'divide-y divide-gray-50';
                catBody.setAttribute('data-categoria', categoria);

                // Toggle colapsar/expandir
                catHeader.addEventListener('click', () => {
                    const isHidden = catBody.classList.toggle('hidden');
                    catHeader.querySelector('.cat-chevron').style.transform = isHidden ? 'rotate(-90deg)' : '';
                });

                productos.forEach(({ productoId, lotes }) => {
                    const nombre = lotes[0]?.producto ?? `Producto #${productoId}`;
                    const inicial = nombre.charAt(0).toUpperCase();

                    // Fila por producto
                    const prodSection = document.createElement('div');
                    prodSection.className = 'px-5 py-3.5';

                    // Header del producto
                    const prodHeader = document.createElement('div');
                    prodHeader.className = 'flex items-center gap-2 mb-3';
                    prodHeader.innerHTML = `
                        <div class="w-7 h-7 rounded-md ${color.bg} flex items-center justify-center shrink-0 ${color.text} font-bold text-xs">
                            ${inicial}
                        </div>
                        <span class="text-sm font-semibold text-gray-800">${nombre}</span>
                        <span class="ml-auto text-xs text-gray-400">${lotes.length} lote${lotes.length !== 1 ? 's' : ''}</span>
                    `;
                    prodSection.appendChild(prodHeader);

                    // Filas de lotes
                    lotes.forEach((lote, index) => {
                        const fila = document.createElement('div');
                        fila.className = 'flex items-center gap-3 ml-9 mb-2 p-2.5 bg-gray-50 rounded-lg border border-gray-100';

                        fila.innerHTML = `
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-mono font-semibold rounded bg-gray-200 text-gray-700">
                                        ${lote.lote}
                                    </span>
                                    ${badgeCaducidad(lote.fecha_caducidad)}
                                </div>
                                <div class="mt-1 text-xs text-gray-400">
                                    <span class="font-semibold text-gray-600">${lote.cantidad}</span> disponibles
                                </div>
                            </div>
                            <div class="shrink-0 w-28">
                                <div class="relative">
                                    <input
                                        type="number"
                                        name="detalles[${productoId}][${index}][cantidad]"
                                        min="0"
                                        max="${lote.cantidad}"
                                        placeholder="0"
                                        class="w-full px-3 py-2 pr-8 text-sm font-semibold text-right transition border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cantidad-input"
                                        data-max="${lote.cantidad}"
                                    />
                                    <span class="absolute text-xs text-gray-400 -translate-y-1/2 right-2 top-1/2">uds</span>
                                </div>
                            </div>
                            <input type="hidden" name="detalles[${productoId}][${index}][lote]" value="${lote.lote}" />
                            <input type="hidden" name="detalles[${productoId}][${index}][fecha_caducidad]" value="${lote.fecha_caducidad}" />
                        `;

                        prodSection.appendChild(fila);
                    });

                    catBody.appendChild(prodSection);
                });

                contenedor.appendChild(catHeader);
                contenedor.appendChild(catBody);
            });

            // Escuchar cambios en inputs de cantidad
            contenedor.querySelectorAll('.cantidad-input').forEach(input => {
                input.addEventListener('input', function () {
                    const max = parseFloat(this.dataset.max);
                    const val = parseFloat(this.value) || 0;
                    if (val > max) {
                        this.value = max;
                        this.classList.add('border-red-400', 'bg-red-50');
                    } else if (val < 0) {
                        this.value = 0;
                    } else {
                        this.classList.remove('border-red-400', 'bg-red-50');
                    }
                    actualizarContador();
                });
            });

            // Generar pills de filtro con las categorías encontradas
            generarPillsCategorias(Object.keys(porCategoria));

            show('contenedor-productos');
            actualizarContador();
        })
        .catch(err => {
            console.error('Error al cargar lotes:', err);
            show('estado-vacio');
        });
});
</script>

</x-app-layout>