{{--
    Componente: ícono de ayuda contextual
    Uso: <x-ayuda-btn modulo="producciones" />
    Muestra un ? que al hover muestra tooltip y al click lleva a la guía.
--}}
@php
    $ayuda = \App\Models\Ayuda::where('modulo', $modulo)
        ->where('activo', true)
        ->whereIn('plataforma', [$plataforma ?? 'web', 'ambas'])
        ->first();
@endphp

@if($ayuda)
<div class="relative inline-block group">
    <a href="{{ route('ayuda.modulo', [$ayuda->modulo, $plataforma ?? 'web']) }}"
       class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-indigo-600 transition bg-indigo-100 rounded-full cursor-pointer hover:bg-indigo-200"
       target="_blank" rel="noopener noreferrer" title="Ver ayuda">
        ?
    </a>

    {{-- Tooltip — pointer-events-none para no bloquear el clic del enlace --}}
    @if($ayuda->descripcion_corta)
    <div class="absolute z-50 hidden w-56 mb-2 -translate-x-1/2 pointer-events-none bottom-full left-1/2 group-hover:block">
        <div class="p-3 text-xs text-white bg-gray-900 rounded-lg shadow-lg">
            <div class="mb-1 font-semibold">{{ $ayuda->titulo }}</div>
            <div class="text-gray-300">{{ $ayuda->descripcion_corta }}</div>
            <div class="mt-2 text-xs text-indigo-400">Clic para ver la guía completa →</div>
        </div>
        <div class="w-2 h-2 mx-auto -mt-1 rotate-45 bg-gray-900"></div>
    </div>
    @endif
</div>
@endif