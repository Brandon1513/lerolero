<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800"> Nueva Guía de Ayuda</h2>
            <a href="{{ route('ayuda.index') }}" class="px-4 py-2 text-sm bg-gray-100 rounded hover:bg-gray-200">← Volver</a>
        </div>
    </x-slot>
    <div class="max-w-4xl py-10 mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('ayuda.store') }}" enctype="multipart/form-data">
            @csrf
            @include('ayuda._form', ['ayuda' => null, 'modulos' => $modulos])
        </form>
    </div>
</x-app-layout>