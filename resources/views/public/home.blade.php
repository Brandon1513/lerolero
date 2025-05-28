<x-app-layout>
    <div class="relative flex flex-col items-center justify-center h-screen bg-center bg-cover " style="background-image: url('{{ asset('images/background-sweets.png') }}');">
        <div class="max-w-xl p-8 px-4 text-center bg-white rounded-lg shadow-lg bg-opacity-80 backdrop-blur-sm sm:px-6 lg:px-8 lg:max-w-7xl">
            <!-- Logo Principal -->
            <img src="{{ asset('images/logo.png') }}" alt="LeroLero Logo" class="w-40 mx-auto mb-4">

            <!-- Título Principal -->
            <h2 class="text-4xl font-extrabold leading-8 tracking-tight text-[#9C27B0] sm:text-6xl">
                Bienvenido a LeroLero 🍬
            </h2>

            <!-- Descripción -->
            <p class="max-w-3xl mx-auto mt-4 text-xl text-gray-800">
                Especialistas en dulces a granel y por paquete para tu negocio.
                <br>Explora nuestro catálogo y endulza tu día con LeroLero.
            </p>

            <!-- Botón de catálogo o login -->
            <div class="mt-6">
                <a href="{{ route('login') }}" class="inline-block px-6 py-3 text-base font-medium text-white bg-[#FFEB3B] text-[#9C27B0] border border-transparent rounded-md hover:bg-yellow-400">
                    Iniciar Sesión
                </a>
            </div>
        </div>

        <!-- Logo en la esquina inferior derecha -->
        <img src="{{ asset('images/logo.png') }}" alt="LeroLero Logo" class="absolute w-32 bottom-5 right-5 opacity-90">
    </div>
</x-app-layout>
