<x-app-layout>
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center px-6">
            <div class="text-6xl mb-4">🔒</div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Acceso restringido</h1>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                No tienes permiso para ver este módulo.<br>
                Si crees que es un error, contacta al administrador.
            </p>
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                ← Regresar
            </a>
        </div>
    </div>
</x-app-layout>