<x-app-layout>
  <div class="relative flex flex-col items-center justify-center h-screen bg-center bg-cover"
       style="background-image: url('{{ asset('images/background-sweets.png') }}');">

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

      <!-- CTAs -->
      <div class="flex flex-col items-center justify-center gap-3 mt-6 sm:flex-row">
        <a href="{{ route('login') }}"
           class="inline-block px-6 py-3 text-base font-semibold text-[#9C27B0] bg-[#FFEB3B] rounded-md hover:bg-yellow-400">
          Iniciar Sesión
        </a>

        <!-- Descargar APK -->
        <a href="{{ route('app.download') }}"
           class="inline-flex items-center gap-2 px-6 py-3 text-base font-semibold text-white bg-[#9C27B0] rounded-md hover:bg-purple-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V3"/>
          </svg>
          Descargar App Android (APK)
        </a>
      </div>

      <!-- QR + Instrucciones -->
      <div class="grid max-w-4xl grid-cols-1 gap-6 mx-auto mt-8 sm:grid-cols-2">
        <div class="flex flex-col items-center justify-center p-4 bg-white rounded-lg shadow-sm">
          <p class="mb-2 text-sm font-semibold text-gray-700">Escanea para descargar</p>
          <img
            alt="QR descarga APK"
            class="w-40 h-40"
            src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('app.download')) }}"
          >
          <p class="mt-2 text-xs text-gray-500 break-all select-all">
            {{ route('app.download') }}
          </p>
        </div>

        <div class="p-4 text-left bg-white rounded-lg shadow-sm">
          <p class="mb-2 text-sm font-semibold text-gray-700">Instalar en Android</p>
          <ol class="pl-5 space-y-1 text-sm text-gray-700 list-decimal">
            <li>Toca “Descargar App Android (APK)”.</li>
            <li>Abre el archivo descargado (<span class="font-mono">LeroLero-v1.apk</span>).</li>
            <li>Si Android lo pide, permite instalar desde el navegador (Fuentes desconocidas).</li>
            <li>Listo: abre <strong>LeroLero</strong> e inicia sesión.</li>
          </ol>
          <p class="mt-3 text-xs text-gray-500">iOS: instalación directa no disponible fuera de App Store.</p>
        </div>
      </div>
    </div>

    <!-- Logo en la esquina inferior derecha -->
    <img src="{{ asset('images/logo.png') }}" alt="LeroLero Logo" class="absolute w-32 bottom-5 right-5 opacity-90">
  </div>
</x-app-layout>
