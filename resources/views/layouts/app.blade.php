@php
    $links = [
        [
            'header' => 'Principal',
        ],
        [
            'name' => 'Dashboard',
            'icon' => 'svg/dashboard.svg',
            'route' => 'admin.dashboard',
            'active' => 'admin.dashboard',
        ],
        [
            'name' => 'Inventario',
            'icon' => 'svg/packages.svg',
            'active' => ['admin.categories.*', 'admin.products.*', 'admin.warehouses.*'],
            'submenu' => [
                [
                    'name' => 'Categorías',
                    'route' => 'admin.categories.index',
                    'icon' => 'svg/list-check.svg',
                    'active' => 'admin.categories.*',
                ],
                [
                    'name' => 'Productos',
                    'route' => 'admin.products.index',
                    'icon' => 'svg/box.svg',
                    'active' => 'admin.products.*',
                ],
                [
                    'name' => 'Almacenes',
                    'route' => 'admin.warehouses.index',
                    'icon' => 'svg/building-warehouse.svg',
                    'active' => 'admin.warehouses.*',
                ],
            ],
        ],

        [
            'header' => 'Gestion',
        ],

        [
            'name' => 'Compras',
            'icon' => 'svg/shopping-cart.svg',
            'active' => ['admin.suppliers.*', 'admin.purchase_orders.*', 'admin.purchases.*'],
            'submenu' => [
                [
                    'name' => 'Proveedores',
                    'icon' => 'svg/truck.svg',
                    'route' => 'admin.suppliers.index',
                    'active' => 'admin.suppliers.*',
                ],
                [
                    'name' => 'Ordenes de compra',
                    'icon' => 'svg/report-money.svg',
                    'route' => 'admin.purchase_orders.index',
                    'active' => 'admin.purchase_orders.*',
                ],
                [
                    'name' => 'Compras',
                    'icon' => 'svg/clipboard-check.svg',
                    'route' => 'admin.purchases.index',
                    'active' => 'admin.purchases.*',
                ],
            ],
        ],
        [
            'name' => 'Ventas',
            'icon' => 'svg/cash-register.svg',
            'active' => ['admin.customers.*', 'admin.quotes.*', 'admin.sales.*'],
            'submenu' => [
                [
                    'name' => 'Clientes',
                    'icon' => 'svg/users.svg',
                    'route' => 'admin.customers.index',
                    'active' => 'admin.customers.*',
                ],
                [
                    'name' => 'Cotizaciones',
                    'icon' => 'svg/clipboard-list.svg',
                    'route' => 'admin.quotes.index',
                    'active' => 'admin.quotes.*',
                ],
                [
                    'name' => 'Ventas',
                    'icon' => 'svg/shopping-cart-copy.svg',
                    'route' => 'admin.sales.index',
                    'active' => 'admin.sales.*',
                ],
            ],
        ],

        [
            'name' => 'Movimientos',
            'icon' => 'svg/rotate-clockwise.svg',
            'active' => ['admin.movements.*', 'admin.transfers.*'],
            'submenu' => [
                [
                    'name' => 'Entradas y Salidas',
                    'icon' => 'svg/arrows-exchange.svg',
                    'route' => 'admin.movements.index',
                    'active' => 'admin.movements.*',
                ],
                [
                    'name' => 'Transferencias',
                    'icon' => 'svg/transfer-in.svg',
                    'route' => 'admin.transfers.index',
                    'active' => 'admin.transfers.*',
                ],
            ],
        ],

        [
            'name' => 'Reportes',
            'icon' => 'svg/chart-bar.svg',
            'active' => ['admin.reports.*'],
            'submenu' => [
                [
                    'name' => 'Productos más vendidos',
                    'icon' => 'svg/arrows-exchange.svg',
                    'route' => 'admin.reports.top-products',
                    'active' => 'admin.reports.top-products',
                ],
                [
                    'name' => 'Productos con poco stock',
                    'icon' => 'svg/transfer-in.svg',
                    'route' => 'admin.reports.low-stock',
                    'active' => 'admin.reports.low-stock',
                ],
                [
                    'name' => 'Clientes más frecuentes',
                    'icon' => 'svg/transfer-in.svg',
                    'route' => 'admin.reports.top-customers',
                    'active' => 'admin.reports.top-customers',
                ],
            ],
        ],

        [
            'header' => 'Configuraión',
        ],

        [
            'name' => 'Usuarios',
            'icon' => 'svg/users.svg',
            'route' => 'admin.customers.index',
            'active' => ['admin.categories.*'],
        ],

        [
            'name' => 'Roles',
            'icon' => 'svg/circles.svg',
            'route' => 'admin.customers.index',
            'active' => ['admin.categories.*'],
        ],

        [
            'name' => 'Permisos',
            'icon' => 'svg/shield-lock.svg',
            'route' => 'admin.customers.index',
            'active' => ['admin.categories.*'],
        ],

        [
            'name' => 'Ajustes',
            'icon' => 'svg/settings.svg',
            'route' => 'admin.customers.index',
            'active' => ['admin.categories.*'],
        ],
    ];
@endphp

@props([
    'title' => config('app.name', 'Inventarios'),
    'breadcrumbs' => [],
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="darkModeControl" :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>

    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <wireui:scripts />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @livewireStyles
    @stack('css')
</head>

<body
    class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">

    <div x-data="{ sidebarIsOpen: false }" class="relative flex min-h-screen">

        <div x-cloak x-show="sidebarIsOpen" class="fixed inset-0 z-20 bg-gray-900/60 backdrop-blur-sm md:hidden"
            x-on:click="sidebarIsOpen = false" x-transition:enter="transition opacity-ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

        <aside x-cloak
            class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-transform duration-300 transform md:translate-x-0 md:static md:inset-0"
            :class="sidebarIsOpen ? 'translate-x-0' : '-translate-x-full'">

            <div class="flex flex-col h-full">
                <div class="flex items-center justify-center h-16 border-b border-gray-100 dark:border-gray-800">
                    <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-blue-600 dark:text-blue-400">
                        {{ config('app.name') }}
                    </a>
                </div>

                <nav class="flex-1 overflow-y-auto p-4 space-y-1 custom-scrollbar">
                    @foreach ($links as $link)
                        @isset($link['header'])
                            <div class="pt-4 pb-1 pl-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                {{ $link['header'] }}
                            </div>
                        @else
                            @isset($link['submenu'])
                                {{-- Corregido: Detecta si alguna ruta del submenú está activa para expandirlo --}}
                                <div x-data="{ isExpanded: {{ request()->routeIs($link['active']) ? 'true' : 'false' }} }" class="space-y-1">
                                    <button @click="isExpanded = !isExpanded"
                                        class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium transition-colors rounded-lg group hover:bg-gray-100 dark:hover:bg-gray-800"
                                        :class="isExpanded ?
                                            'text-blue-600 dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10' :
                                            'text-gray-600 dark:text-gray-400'">

                                        <div class="flex items-center gap-3">
                                            <span class="w-5 h-5 flex items-center justify-center">
                                                {{-- Corregido: Verificación de icono con path relativo correcto --}}
                                                @if (isset($link['icon']) && file_exists(public_path($link['icon'])))
                                                    {!! file_get_contents(public_path($link['icon'])) !!}
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </span>
                                            <span>{{ $link['name'] }}</span>
                                        </div>

                                        <svg class="w-4 h-4 transition-transform duration-200"
                                            :class="isExpanded ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="isExpanded" x-collapse x-cloak class="pl-10 space-y-1">
                                        @foreach ($link['submenu'] as $item)
                                            <a href="{{ route($item['route']) }}"
                                                class="flex items-center gap-3 px-3 py-2 text-sm transition-colors rounded-lg group
                                                {{ request()->routeIs($item['active'])
                                                    ? 'text-blue-600 font-semibold dark:text-blue-400 bg-blue-50/50 dark:bg-blue-900/10'
                                                    : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800' }}">

                                                {{-- Contenedor del Icono --}}
                                                <span class="w-5 h-5 flex-shrink-0 flex items-center justify-center">
                                                    @if (isset($item['icon']) && file_exists(public_path($item['icon'])))
                                                        {{-- El icono heredará el color del texto gracias a 'currentColor' en el SVG --}}
                                                        {!! file_get_contents(public_path($item['icon'])) !!}
                                                    @else
                                                        {{-- Icono por defecto si no existe el archivo --}}
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                            </path>
                                                        </svg>
                                                    @endif
                                                </span>

                                                {{-- Texto del Item --}}
                                                <span class="truncate">{{ $item['name'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <a href="{{ route($link['route']) }}"
                                    class="flex items-center gap-3 px-3 py-2 text-sm font-medium transition-all duration-200 rounded-lg group {{ request()->routeIs($link['active']) ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
                                    <span class="w-5 h-5 flex items-center justify-center">
                                        @if (isset($link['icon']) && file_exists(public_path($link['icon'])))
                                            {!! file_get_contents(public_path($link['icon'])) !!}
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                        @endif
                                    </span>
                                    <span>{{ $link['name'] }}</span>
                                </a>
                            @endisset
                        @endisset
                    @endforeach
                </nav>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

            <header
                class="flex items-center justify-between h-16 px-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-10">
                <div class="flex items-center">
                    <button @click="sidebarIsOpen = true"
                        class="p-2 text-gray-500 rounded-lg md:hidden hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <nav class="hidden ml-4 md:flex items-center space-x-2 text-sm">
                        @foreach ($breadcrumbs as $index => $item)
                            @if (!$loop->first)
                                <span class="text-gray-400">/</span>
                            @endif
                            @isset($item['href'])
                                <a href="{{ $item['href'] }}"
                                    class="text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $item['name'] }}</a>
                            @else
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $item['name'] }}</span>
                            @endisset
                        @endforeach
                    </nav>
                </div>

                <div class="flex items-center space-x-3">
                    <button @click="toggleTheme"
                        class="p-2 text-gray-500 rounded-xl bg-gray-100 dark:bg-gray-800 dark:text-yellow-400 hover:ring-2 ring-blue-500 transition-all">
                        <template x-if="!darkMode">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </template>
                        <template x-if="darkMode">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </template>
                    </button>

                    <div class="relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="flex items-center text-sm transition border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300">
                                    <img class="size-9 rounded-full object-cover shadow-sm"
                                        src="{{ Auth::user()->profile_photo_url }}"
                                        alt="{{ Auth::user()->name }}" />
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Manage Account') }}</div>
                                <x-dropdown-link
                                    href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link>
                                <div class="border-t border-gray-200 dark:border-gray-700"></div>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}"
                                        @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </header>

            <main id="main-content"
                class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-950 p-6 md:p-10">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('modals')
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('darkModeControl', () => ({
                darkMode: localStorage.getItem('darkMode') === 'true',
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('darkMode', this.darkMode);
                }
            }))
        })
    </script>

    <script>
        Livewire.on('swal', (data) => {
            Swal.fire(data[0]);
        })
    </script>

    @if (session('swal'))
        <script>
            Swal.fire(@json(session('swal')));
        </script>
    @endif
    @stack('js')
</body>

</html>
