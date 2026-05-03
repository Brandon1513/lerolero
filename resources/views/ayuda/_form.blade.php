<div class="space-y-6">

    {{-- Info básica --}}
    <div class="p-6 space-y-4 bg-white rounded-lg shadow">
        <h3 class="font-bold text-gray-700">Información básica</h3>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Módulo *</label>
                <select name="modulo" required class="w-full p-2 text-sm border rounded-lg">
                    @foreach($modulos as $key => $mod)
                        <option value="{{ $key }}" {{ old('modulo', $ayuda?->modulo) === $key ? 'selected' : '' }}>
                            {{ $mod['icono'] }} {{ $mod['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Plataforma *</label>
                <select name="plataforma" required class="w-full p-2 text-sm border rounded-lg">
                    <option value="web"   {{ old('plataforma', $ayuda?->plataforma) === 'web'   ? 'selected' : '' }}>🌐 Web (Admin)</option>
                    <option value="movil" {{ old('plataforma', $ayuda?->plataforma) === 'movil' ? 'selected' : '' }}>📱 Móvil (Vendedores)</option>
                    <option value="ambas" {{ old('plataforma', $ayuda?->plataforma) === 'ambas' ? 'selected' : '' }}>🌐📱 Ambas</option>
                </select>
            </div>
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">Título *</label>
            <input type="text" name="titulo" required value="{{ old('titulo', $ayuda?->titulo) }}"
                   placeholder="ej: Cómo registrar una producción"
                   class="w-full p-2 text-sm border rounded-lg">
        </div>
        <div>
            <label class="block mb-1 text-sm font-medium text-gray-700">
                Descripción corta
                <span class="font-normal text-gray-400">(aparece en el tooltip del botón flotante)</span>
            </label>
            <input type="text" name="descripcion_corta" value="{{ old('descripcion_corta', $ayuda?->descripcion_corta) }}"
                   placeholder="ej: Aprende a registrar una producción en 3 pasos"
                   class="w-full p-2 text-sm border rounded-lg">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block mb-1 text-sm font-medium text-gray-700">Orden</label>
                <input type="number" name="orden" min="0" value="{{ old('orden', $ayuda?->orden ?? 0) }}"
                       class="w-full p-2 text-sm border rounded-lg">
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="activo" id="activo" value="1"
                       {{ old('activo', $ayuda?->activo ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 text-indigo-600 rounded">
                <label for="activo" class="text-sm font-medium text-gray-700">Activo (visible)</label>
            </div>
        </div>
    </div>

    {{-- Pasos --}}
    <div class="p-6 bg-white rounded-lg shadow">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-700">📋 Pasos</h3>
            <button type="button" onclick="agregarPaso()"
                    class="px-3 py-1.5 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                + Agregar paso
            </button>
        </div>

        <div id="contenedor-pasos" class="space-y-4">
            @if($ayuda && count($ayuda->pasos ?? []) > 0)
                @foreach($ayuda->pasos as $i => $paso)
                <div class="p-4 space-y-3 border paso-item rounded-xl bg-gray-50">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-indigo-600 paso-num">Paso {{ $i + 1 }}</span>
                        <button type="button" onclick="eliminarPaso(this)" class="text-xs text-red-400 hover:text-red-600">✕ Eliminar</button>
                    </div>
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                        <input type="text" name="paso_icono[]" value="{{ $paso['icono'] ?? '' }}"
                               placeholder="Emoji ej: 📦" class="p-2 text-sm border rounded">
                        <input type="text" name="paso_titulo[]" value="{{ $paso['titulo'] ?? '' }}"
                               placeholder="Título del paso *" class="p-2 text-sm border rounded md:col-span-2">
                        <textarea name="paso_descripcion[]" rows="2"
                                  placeholder="Descripción del paso..."
                                  class="p-2 text-sm border rounded md:col-span-3">{{ $paso['descripcion'] ?? '' }}</textarea>
                    </div>
                    {{-- Imagen del paso --}}
                    <div class="pt-3 space-y-2 border-t">
                        <p class="text-xs font-semibold text-gray-500">🖼 Imagen de este paso (opcional)</p>
                        @if(!empty($paso['imagen_url']))
                            <div class="flex items-center gap-3">
                                <img src="{{ $paso['imagen_url'] }}" class="object-cover w-20 border rounded h-14">
                                <span class="text-xs text-gray-500">Imagen actual</span>
                            </div>
                        @endif
                        <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                            <div>
                                <label class="block mb-1 text-xs text-gray-500">URL de imagen</label>
                                <input type="text" name="paso_imagen_url[]"
                                       value="{{ ($paso['imagen_tipo'] ?? '') === 'url' ? ($paso['imagen_url'] ?? '') : '' }}"
                                       placeholder="https://..."
                                       class="w-full p-2 text-xs border rounded">
                            </div>
                            <div>
                                <label class="block mb-1 text-xs text-gray-500">O subir archivo</label>
                                <input type="file" name="paso_imagen_archivo[]"
                                       accept="image/*"
                                       class="w-full border rounded p-1.5 text-xs">
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
        <p class="mt-3 text-xs text-gray-400">Cada paso puede tener su propia imagen de referencia.</p>
    </div>

    {{-- Galería general --}}
    <div class="p-6 space-y-4 bg-white rounded-lg shadow">
        <h3 class="font-bold text-gray-700">🖼 Galería de imágenes</h3>
        <p class="text-xs text-gray-500">Estas imágenes aparecen al final de la guía como galería de referencia.</p>

        {{-- Imágenes existentes --}}
        @if($ayuda && count($ayuda->imagenes ?? []) > 0)
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            @foreach($ayuda->imagenes as $i => $img)
            <div class="relative overflow-hidden border rounded-lg group">
                <img src="{{ $img['url'] }}" class="object-cover w-full h-28">
                <div class="absolute inset-0 flex items-center justify-center transition bg-black bg-opacity-0 group-hover:bg-opacity-40">
                    <label class="items-center hidden gap-1 cursor-pointer group-hover:flex">
                        <input type="checkbox" name="eliminar_imagen[]" value="{{ $i }}"
                               class="w-4 h-4 accent-red-500">
                        <span class="text-xs font-semibold text-white">Eliminar</span>
                    </label>
                </div>
                <div class="p-1 text-xs text-gray-400 truncate">
                    {{ $img['tipo'] === 'upload' ? '📁 Archivo' : '🔗 URL' }}
                </div>
            </div>
            @endforeach
        </div>
        <p class="text-xs text-gray-400">Marca las imágenes que deseas eliminar y guarda.</p>
        @endif

        {{-- Agregar nuevas imágenes --}}
        <div class="pt-4 space-y-3 border-t">
            <p class="text-sm font-semibold text-gray-600">Agregar imágenes</p>

            {{-- Por archivo --}}
            <div>
                <label class="block mb-1 text-xs font-medium text-gray-600">📁 Subir archivos (jpg, png, gif)</label>
                <input type="file" name="imagen_archivo_nueva[]" accept="image/*" multiple
                       class="w-full p-2 text-sm border rounded-lg">
                <p class="mt-1 text-xs text-gray-400">Puedes seleccionar múltiples archivos.</p>
            </div>

            {{-- Por URL --}}
            <div>
                <label class="block mb-1 text-xs font-medium text-gray-600">🔗 Agregar por URL</label>
                <div id="urls-container" class="space-y-2">
                    <div class="flex gap-2">
                        <input type="text" name="imagen_url_nueva[]" placeholder="https://..."
                               class="flex-1 p-2 text-sm border rounded-lg">
                        <button type="button" onclick="agregarUrl()"
                                class="px-3 py-1 text-xs bg-gray-200 rounded hover:bg-gray-300">+ URL</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenido adicional --}}
    <div class="p-6 bg-white rounded-lg shadow">
        <h3 class="mb-3 font-bold text-gray-700">
            📄 Notas adicionales
            <span class="text-sm font-normal text-gray-400">(opcional)</span>
        </h3>
        <textarea name="contenido" rows="6"
                  placeholder="Notas extra, advertencias, ejemplos..."
                  class="w-full p-3 text-sm border rounded-lg">{{ old('contenido', $ayuda?->contenido) }}</textarea>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('ayuda.index') }}" class="px-6 py-2 text-sm bg-gray-100 rounded-lg hover:bg-gray-200">Cancelar</a>
        <button type="submit" class="px-6 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
            {{ $ayuda ? 'Guardar cambios' : 'Crear guía' }}
        </button>
    </div>
</div>

<script>
function agregarPaso() {
    const cont = document.getElementById('contenedor-pasos');
    const num = cont.querySelectorAll('.paso-item').length + 1;
    const div = document.createElement('div');
    div.className = 'paso-item border rounded-xl p-4 bg-gray-50 space-y-3';
    div.innerHTML = `
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-indigo-600 paso-num">Paso ${num}</span>
            <button type="button" onclick="eliminarPaso(this)" class="text-xs text-red-400 hover:text-red-600">✕ Eliminar</button>
        </div>
        <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
            <input type="text" name="paso_icono[]" placeholder="Emoji ej: 📦" class="p-2 text-sm border rounded">
            <input type="text" name="paso_titulo[]" placeholder="Título del paso *" class="p-2 text-sm border rounded md:col-span-2">
            <textarea name="paso_descripcion[]" rows="2" placeholder="Descripción del paso..."
                      class="p-2 text-sm border rounded md:col-span-3"></textarea>
        </div>
        <div class="pt-3 space-y-2 border-t">
            <p class="text-xs font-semibold text-gray-500">🖼 Imagen de este paso (opcional)</p>
            <div class="grid grid-cols-1 gap-2 md:grid-cols-2">
                <div>
                    <label class="block mb-1 text-xs text-gray-500">URL de imagen</label>
                    <input type="text" name="paso_imagen_url[]" placeholder="https://..." class="w-full p-2 text-xs border rounded">
                </div>
                <div>
                    <label class="block mb-1 text-xs text-gray-500">O subir archivo</label>
                    <input type="file" name="paso_imagen_archivo[]" accept="image/*" class="w-full border rounded p-1.5 text-xs">
                </div>
            </div>
        </div>
    `;
    cont.appendChild(div);
    renumerarPasos();
}

function eliminarPaso(btn) {
    btn.closest('.paso-item').remove();
    renumerarPasos();
}

function renumerarPasos() {
    document.querySelectorAll('.paso-num').forEach((el, i) => {
        el.textContent = `Paso ${i + 1}`;
    });
}

function agregarUrl() {
    const cont = document.getElementById('urls-container');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" name="imagen_url_nueva[]" placeholder="https://..."
               class="flex-1 p-2 text-sm border rounded-lg">
        <button type="button" onclick="this.parentElement.remove()"
                class="px-3 py-1 text-xs text-red-600 bg-red-100 rounded hover:bg-red-200">✕</button>
    `;
    cont.appendChild(div);
}
</script>