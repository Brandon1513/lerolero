<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
                    </a>
                </div>

                <!-- Navigation Links -->
                @if (Auth::check())
                <div class="hidden space-x-8 sm:flex sm:items-center sm:ms-6">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Inicio') }}
                    </x-nav-link>
                    <div class="relative">
                        <x-dropdown align="left">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out bg-transparent border border-transparent rounded-md hover:text-gray-300 focus:outline-none">
                                    <div>{{ __('Administración') }}</div>
                                    <div class="ms-1">
                                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                            <!-- Gestionar Jefes -->
                            @if (Auth::check() && Auth::user()->hasRole('administrador'))
                                <x-dropdown-link :href="route('clientes.index')" class="text-gray-700 hover:bg-gray-200">
                                    {{ __('Clientes') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('vendedores.index')" class="text-gray-700 hover:bg-gray-200">
                                    {{ __('Vendedores') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Opción "Gestionar Niveles de precio" visible para administrador y recursos_humanos -->
                            @if (Auth::check() && Auth::user()->hasRole('administrador'))
                                <x-dropdown-link :href="route('niveles-precio.index')" class="text-gray-700 hover:bg-gray-200">
                                    {{ __('Niveles de precio') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Opción "Gestionar Unidades" solo visible para administrador -->
                            @if (Auth::check() && Auth::user()->hasRole('administrador'))
                                <x-dropdown-link :href="route('unidades.index')" class="text-gray-700 hover:bg-gray-200">
                                    {{ __('Unidades') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Opción "Gestionar Productos" solo visible para administrador -->
                            @if (Auth::check() && Auth::user()->hasRole('administrador'))
                                <x-dropdown-link :href="route('productos.index')" class="text-gray-700 hover:bg-gray-200">
                                    {{ __('Productos') }}
                                </x-dropdown-link>
                            @endif

                            <!-- Opción "Gestionar Categorías" solo visible para administrador -->
                            @if (Auth::check() && Auth::user()->hasRole('administrador'))
                                <x-dropdown-link :href="route('categorias.index')" class="text-gray-700 hover:bg-gray-200">
                                    {{ __('Categorías') }}
                                </x-dropdown-link>
                            @endif
                            <!-- Opción "Gestionar Producciones" solo visible para administrador -->
                            @if (Auth::check() && Auth::user()->hasRole('administrador'))
                                <x-dropdown-link :href="route('producciones.index')" class="text-gray-700 hover:bg-gray-200">
                                    {{ __('Producciones') }}
                                </x-dropdown-link>
                            @endif
                        </x-slot>

                        </x-dropdown>
                    </div>

                    <!-- Menú Almacenes -->
                    <div class="relative">
                        <x-dropdown align="left">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out bg-transparent border border-transparent rounded-md hover:text-gray-300 focus:outline-none">
                                    <div>{{ __('Almacenes') }}</div>
                                    <div class="ms-1">
                                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                @if (Auth::check() && Auth::user()->hasRole('administrador'))
                                    <x-dropdown-link :href="route('almacenes.index')" class="text-gray-700 hover:bg-gray-200">
                                        {{ __('Almacenes') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('inventario.index')" class="text-gray-700 hover:bg-gray-200">
                                        {{ __('Inventario') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('traslados.index')" class="text-gray-700 hover:bg-gray-200">
                                        {{ __('Traslados de Inventario') }}
                                    </x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>
                    <!-- Menú ventas -->
                    <div class="relative">
                        <x-dropdown align="left">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out bg-transparent border border-transparent rounded-md hover:text-gray-300 focus:outline-none">
                                    <div>{{ __('Ventas') }}</div>
                                    <div class="ms-1">
                                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                @if (Auth::check() && Auth::user()->hasRole('administrador'))
                                    <x-dropdown-link :href="route('ventas.index')" class="text-gray-700 hover:bg-gray-200">
                                        {{ __(' ventas') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('ventas.panel')" class="text-gray-700 hover:bg-gray-200">
                                        {{ __('Panel de Ventas') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('cierres.index')" class="text-gray-700 hover:bg-gray-200">
                                        {{ __('Cierre de Ruta') }}
                                    </x-dropdown-link>
                                @endif
                            </x-slot>
                        </x-dropdown>
                    </div>

                </div>
            </div>
            
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                            <div>{{ Auth::check() ? Auth::user()->name : 'Iniciar Sesión' }}</div>

                            <div class="ms-1">
                                <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @else
            <!-- Solo botones para visitantes no autenticados -->
                <div class="hidden sm:flex sm:items-center sm:ms-6">
                    <a href="{{ route('login') }}" class="text-sm text-gray-700 underline hover:text-gray-900">Iniciar Sesión</a>
                </div>
            @endif

            <!-- Hamburger -->
            <div class="flex items-center -me-2 sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                    <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        @if (Auth::check())
        <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="text-base font-medium">{{ Auth::user()->name }}</div>
                    <div class="text-sm font-medium">{{ Auth::user()->email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')" >
                            {{ __('Perfil') }}
                        </x-responsive-nav-link>
                    
                    @if(Auth::user()->hasRole('administrador'))
                        <x-responsive-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.index')" >
                        {{ __('Clientes') }}
                        </x-responsive-nav-link>
                    @endif

                    @if(Auth::user()->hasRole('administrador'))
                        <x-responsive-nav-link :href="route('vendedores.index')" :active="request()->routeIs('vendedores.index')" >
                        {{ __('Vendedores') }}
                        </x-responsive-nav-link>
                    @endif
                    
                    @if(Auth::user()->hasRole('administrador'))
                        <x-responsive-nav-link :href="route('niveles-precio.index')" :active="request()->routeIs('niveles-precio.index')" >
                        {{ __('Nivel de precio') }}
                        </x-responsive-nav-link>
                    @endif 

                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('unidades.index')" :active="request()->routeIs('unidades.index')" >
                        {{ __('Unidades') }}
                        </x-responsive-nav-link>
                    @endif 
                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.index')" >
                        {{ __('Productos') }}
                        </x-responsive-nav-link>
                    @endif 
                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.index')" >
                        {{ __('Categorias') }}
                        </x-responsive-nav-link>
                    @endif 
                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('inventario.index')" :active="request()->routeIs('inventario.index')" >
                        {{ __('Inventarios') }}
                        </x-responsive-nav-link>
                    @endif 
                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('almacenes.index')" :active="request()->routeIs('almacenes.index')" >
                        {{ __('Almacenes') }}
                        </x-responsive-nav-link>
                    @endif 
                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('traslados.index')" :active="request()->routeIs('traslados.index')" >
                        {{ __('Traslados') }}
                        </x-responsive-nav-link>
                    @endif 
                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('ventas.index')" :active="request()->routeIs('ventas.index')" >
                        {{ __('Ventas') }}
                        </x-responsive-nav-link>
                    @endif 
                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('ventas.panel')" :active="request()->routeIs('ventas.panel')" >
                        {{ __('Panel de ventas') }}
                        </x-responsive-nav-link>
                    @endif 
                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('cierres.index')" :active="request()->routeIs('cierres.index')" >
                        {{ __('Cierres de Venta') }}
                        </x-responsive-nav-link>
                    @endif 
                    @if(Auth::user()->hasRole('administrador') || Auth::user()->hasRole('recursos_humanos'))
                        <x-responsive-nav-link :href="route('producciones.index')" :active="request()->routeIs('producciones.index')" >
                        {{ __('Producciones') }}
                        </x-responsive-nav-link>
                    @endif 
                    
                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-responsive-nav-link :href="route('logout')" 
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Salir') }}
                            </x-responsive-nav-link>
                        </form>
                </div>
            </div>
            
        @else
         <!-- Mostrar link de iniciar sesión si no está logueado en menú responsive -->
            <div class="pt-4 pb-1 border-t border-gray-200">
                <x-responsive-nav-link :href="route('login')" >
                    {{ __('Iniciar Sesión') }}
                </x-responsive-nav-link>
            </div>
        @endif
    </div>
</nav>
