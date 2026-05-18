<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="flex items-center shrink-0">
                    @php
                        // Para empleado_interno: redirigir al primer módulo que tenga permiso de ver
                        if (Auth::check() && Auth::user()->hasRole('empleado_interno') && !Auth::user()->hasRole('administrador')) {
                            $modulos = [
                                'producciones.ver'   => 'producciones.index',
                                'inventario.ver'     => 'inventario.index',
                                'traslados.ver'      => 'traslados.index',
                                'ventas.ver'         => 'ventas.index',
                                'clientes.ver'       => 'clientes.index',
                                'productos.ver'      => 'productos.index',
                                'categorias.ver'     => 'categorias.index',
                                'niveles_precio.ver' => 'niveles-precio.index',
                                'almacenes.ver'      => 'almacenes.index',
                                'cierres.ver'        => 'cierres.index',
                            ];
                            $logoHref = '#';
                            foreach ($modulos as $permiso => $ruta) {
                                if (Auth::user()->can($permiso)) {
                                    $logoHref = route($ruta);
                                    break;
                                }
                            }
                        } else {
                            $logoHref = route('dashboard');
                        }
                    @endphp
                    <a href="{{ $logoHref }}">
                        <x-application-logo class="block w-auto text-gray-800 fill-current h-9" />
                    </a>
                </div>

                @if (Auth::check())
                @php $user = Auth::user(); @endphp
                <div class="hidden space-x-8 sm:flex sm:items-center sm:ms-6">

                    {{-- Inicio solo para admin --}}
                    @if($user->hasRole('administrador'))
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Inicio') }}
                        </x-nav-link>
                    @endif

                    {{-- ── ADMINISTRADOR: menús fijos ── --}}
                    @if($user->hasRole('administrador'))

                        <div class="relative">
                            <x-dropdown align="left">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out bg-transparent border border-transparent rounded-md hover:text-gray-300 focus:outline-none">
                                        <div>{{ __('Administración') }}</div>
                                        <div class="ms-1"><svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('clientes.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Clientes') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('vendedores.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Vendedores') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('niveles-precio.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Niveles de precio') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('unidades.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Unidades') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('productos.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Productos') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('categorias.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Categorías') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('producciones.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Producciones') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('promociones.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Promociones') }}</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <div class="relative">
                            <x-dropdown align="left">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out bg-transparent border border-transparent rounded-md hover:text-gray-300 focus:outline-none">
                                        <div>{{ __('Almacenes') }}</div>
                                        <div class="ms-1"><svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('almacenes.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Almacenes') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('inventario.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Inventario') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('traslados.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Traslados de Inventario') }}</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <div class="relative">
                            <x-dropdown align="left">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 transition duration-150 ease-in-out bg-transparent border border-transparent rounded-md hover:text-gray-300 focus:outline-none">
                                        <div>{{ __('Ventas') }}</div>
                                        <div class="ms-1"><svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                                    </button>
                                </x-slot>
                                <x-slot name="content">
                                    <x-dropdown-link :href="route('ventas.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Ventas') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('ventas.panel')" class="text-gray-700 hover:bg-gray-200">{{ __('Panel de Ventas') }}</x-dropdown-link>
                                    <x-dropdown-link :href="route('cierres.index')" class="text-gray-700 hover:bg-gray-200">{{ __('Cierre de Ruta') }}</x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <x-nav-link :href="route('ayuda.publico')" :active="request()->routeIs('ayuda.*')">
                            📚 {{ __('Ayuda') }}
                        </x-nav-link>

                    @elseif($user->hasRole('empleado_interno'))
                    {{-- ── EMPLEADO INTERNO: dinámico por permisos ──
                         Todas las rutas son las mismas del admin.
                         El middleware 'permiso' controla el acceso.
                         El admin activa/desactiva desde la vista de permisos. --}}

                        @if($user->can('producciones.ver'))
                            <x-nav-link :href="route('producciones.index')" :active="request()->routeIs('producciones.*')">
                                🏭 {{ __('Producciones') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('inventario.ver'))
                            <x-nav-link :href="route('inventario.index')" :active="request()->routeIs('inventario.*')">
                                📦 {{ __('Inventario') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('traslados.ver'))
                            <x-nav-link :href="route('traslados.index')" :active="request()->routeIs('traslados.*')">
                                🚚 {{ __('Traslados') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('ventas.ver'))
                            <x-nav-link :href="route('ventas.index')" :active="request()->routeIs('ventas.*')">
                                🧾 {{ __('Ventas') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('clientes.ver'))
                            <x-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">
                                👥 {{ __('Clientes') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('productos.ver'))
                            <x-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')">
                                🍬 {{ __('Productos') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('categorias.ver'))
                            <x-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">
                                🏷️ {{ __('Categorías') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('niveles_precio.ver'))
                            <x-nav-link :href="route('niveles-precio.index')" :active="request()->routeIs('niveles-precio.*')">
                                💰 {{ __('Niveles precio') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('unidades.ver'))
                            <x-nav-link :href="route('unidades.index')" :active="request()->routeIs('unidades.*')">
                                📏 {{ __('Unidades') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('almacenes.ver'))
                            <x-nav-link :href="route('almacenes.index')" :active="request()->routeIs('almacenes.*')">
                                🏗️ {{ __('Almacenes') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('promociones.ver'))
                            <x-nav-link :href="route('promociones.index')" :active="request()->routeIs('promociones.*')">
                                🔥 {{ __('Promociones') }}
                            </x-nav-link>
                        @endif

                        @if($user->can('cierres.ver'))
                            <x-nav-link :href="route('cierres.index')" :active="request()->routeIs('cierres.*')">
                                📋 {{ __('Cierres') }}
                            </x-nav-link>
                        @endif

                    @endif {{-- fin roles --}}

                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1"><svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg></div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
            @else
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
            @if(Auth::check() && Auth::user()->hasRole('administrador'))
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">{{ __('Dashboard') }}</x-responsive-nav-link>
            @endif
        </div>

        @if (Auth::check())
        @php $user = Auth::user(); @endphp
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="text-base font-medium">{{ $user->name }}</div>
                <div class="text-sm font-medium">{{ $user->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">{{ __('Perfil') }}</x-responsive-nav-link>

                @if($user->hasRole('administrador'))
                    <x-responsive-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">{{ __('Clientes') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('vendedores.index')" :active="request()->routeIs('vendedores.*')">{{ __('Vendedores') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('niveles-precio.index')" :active="request()->routeIs('niveles-precio.*')">{{ __('Nivel de precio') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('unidades.index')" :active="request()->routeIs('unidades.*')">{{ __('Unidades') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')">{{ __('Productos') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">{{ __('Categorias') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('inventario.index')" :active="request()->routeIs('inventario.*')">{{ __('Inventario') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('almacenes.index')" :active="request()->routeIs('almacenes.*')">{{ __('Almacenes') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('traslados.index')" :active="request()->routeIs('traslados.*')">{{ __('Traslados') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('ventas.index')" :active="request()->routeIs('ventas.*')">{{ __('Ventas') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('ventas.panel')" :active="request()->routeIs('ventas.panel')">{{ __('Panel de ventas') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('cierres.index')" :active="request()->routeIs('cierres.*')">{{ __('Cierres de Venta') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('promociones.index')" :active="request()->routeIs('promociones.*')">{{ __('Promociones') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('producciones.index')" :active="request()->routeIs('producciones.*')">{{ __('Producciones') }}</x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('ayuda.publico')" :active="request()->routeIs('ayuda.*')">📚 {{ __('Ayuda') }}</x-responsive-nav-link>

                @elseif($user->hasRole('empleado_interno'))
                    @if($user->can('producciones.ver'))
                        <x-responsive-nav-link :href="route('producciones.index')" :active="request()->routeIs('producciones.*')">🏭 {{ __('Producciones') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('inventario.ver'))
                        <x-responsive-nav-link :href="route('inventario.index')" :active="request()->routeIs('inventario.*')">📦 {{ __('Inventario') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('traslados.ver'))
                        <x-responsive-nav-link :href="route('traslados.index')" :active="request()->routeIs('traslados.*')">🚚 {{ __('Traslados') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('ventas.ver'))
                        <x-responsive-nav-link :href="route('ventas.index')" :active="request()->routeIs('ventas.*')">🧾 {{ __('Ventas') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('clientes.ver'))
                        <x-responsive-nav-link :href="route('clientes.index')" :active="request()->routeIs('clientes.*')">👥 {{ __('Clientes') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('productos.ver'))
                        <x-responsive-nav-link :href="route('productos.index')" :active="request()->routeIs('productos.*')">🍬 {{ __('Productos') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('categorias.ver'))
                        <x-responsive-nav-link :href="route('categorias.index')" :active="request()->routeIs('categorias.*')">🏷️ {{ __('Categorías') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('niveles_precio.ver'))
                        <x-responsive-nav-link :href="route('niveles-precio.index')" :active="request()->routeIs('niveles-precio.*')">💰 {{ __('Niveles precio') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('unidades.ver'))
                        <x-responsive-nav-link :href="route('unidades.index')" :active="request()->routeIs('unidades.*')">📏 {{ __('Unidades') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('almacenes.ver'))
                        <x-responsive-nav-link :href="route('almacenes.index')" :active="request()->routeIs('almacenes.*')">🏗️ {{ __('Almacenes') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('promociones.ver'))
                        <x-responsive-nav-link :href="route('promociones.index')" :active="request()->routeIs('promociones.*')">🔥 {{ __('Promociones') }}</x-responsive-nav-link>
                    @endif
                    @if($user->can('cierres.ver'))
                        <x-responsive-nav-link :href="route('cierres.index')" :active="request()->routeIs('cierres.*')">📋 {{ __('Cierres') }}</x-responsive-nav-link>
                    @endif
                @endif

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Salir') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @else
        <div class="pt-4 pb-1 border-t border-gray-200">
            <x-responsive-nav-link :href="route('login')">{{ __('Iniciar Sesión') }}</x-responsive-nav-link>
        </div>
        @endif
    </div>
</nav>