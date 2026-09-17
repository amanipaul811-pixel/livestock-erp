<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#111827">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <title>@yield('title', 'Livestock ERP')</title>
    <script>
        (function () {
            var stored = localStorage.getItem('theme');
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (stored === 'dark' || (!stored && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/sw.js');
            });
        }
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' };</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100" x-data="{ sidebarOpen: false }">
    @auth
    @php
        $purchaseActive = request()->routeIs(['purchase-orders.*', 'suppliers.*', 'reports.purchases']);
        $inventoryActive = request()->routeIs(['feed-items.*', 'warehouses.*', 'ration-formulas.*', 'reports.stock']);
        $salesActive = request()->routeIs(['sales-orders.*', 'customers.*', 'reports.sales']);
        $reportsActive = request()->routeIs(['reports.health', 'reports.movements']);
    @endphp

    <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-black/50 md:hidden"></div>

    <aside x-cloak
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col border-r border-gray-200 bg-white transition-transform duration-200 ease-in-out dark:border-gray-800 dark:bg-gray-900 md:translate-x-0">
        <div class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-gray-200 px-5 dark:border-gray-800">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c-3 0-5 2-5 5 0 1.5.5 2.5 1 3.5L5 17v3h14v-3l-3-5.5c.5-1 1-2 1-3.5 0-3-2-5-5-5z"/></svg>
                <span class="font-semibold tracking-tight">Livestock ERP</span>
            </div>
            @include('partials.notification-bell')
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4 text-sm">
            @php
                $links = [
                    ['dashboard', route('dashboard'), 'Dashboard', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                    ['calendar', route('calendar'), 'Calendar', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                ];
            @endphp
            @foreach ($links as [$routePattern, $href, $label, $icon])
                <a href="{{ $href }}"
                   class="flex items-center gap-3 rounded-md px-3 py-2 mb-0.5 {{ request()->routeIs($routePattern) ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg>
                    {{ $label }}
                </a>
            @endforeach

            {{-- Purchase: everything to do with buying (animals, feed, medicine) --}}
            <div x-data="{ open: {{ $purchaseActive ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex w-full items-center justify-between rounded-md px-3 py-2 mb-0.5 {{ $purchaseActive ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 2a1 1 0 00-1 1v1H5a2 2 0 00-2 2v13a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2h-3V3a1 1 0 00-1-1H9z"/></svg>
                        Purchase
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.14l3.71-3.91a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="open" x-cloak>
                    <a href="{{ route('purchase-orders.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('purchase-orders.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Purchase Orders</a>
                    <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('suppliers.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Suppliers</a>
                    @if (auth()->user()->hasPermission('dashboard.view'))
                        <a href="{{ route('reports.purchases') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('reports.purchases') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Purchases Report</a>
                    @endif
                </div>
            </div>

            {{-- Inventory: feed stock and what it's made of --}}
            <div x-data="{ open: {{ $inventoryActive ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex w-full items-center justify-between rounded-md px-3 py-2 mb-0.5 {{ $inventoryActive ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7l9-4 9 4-9 4-9-4zm0 5l9 4 9-4M3 7v10l9 4 9-4V7"/></svg>
                        Inventory
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.14l3.71-3.91a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="open" x-cloak>
                    <a href="{{ route('feed-items.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('feed-items.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Feed Items</a>
                    <a href="{{ route('warehouses.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('warehouses.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Warehouses</a>
                    <a href="{{ route('ration-formulas.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('ration-formulas.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Ration Formulas</a>
                    @if (auth()->user()->hasPermission('dashboard.view'))
                        <a href="{{ route('reports.stock') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('reports.stock') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Stock Report</a>
                    @endif
                </div>
            </div>

            {{-- Sales: selling animals and who to --}}
            <div x-data="{ open: {{ $salesActive ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex w-full items-center justify-between rounded-md px-3 py-2 mb-0.5 {{ $salesActive ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    <span class="flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8c-1.5 0-3 .5-3 2s1.5 2 3 2 3 .5 3 2-1.5 2-3 2m0-8V6m0 12v-2M3 12a9 9 0 1018 0 9 9 0 00-18 0z"/></svg>
                        Sales
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.14l3.71-3.91a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="open" x-cloak>
                    @if (auth()->user()->hasPermission('salesorder.create'))
                        <a href="{{ route('sales-orders.create') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('sales-orders.create') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">New Sale</a>
                    @endif
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('customers.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Customers</a>
                    @if (auth()->user()->hasPermission('dashboard.view'))
                        <a href="{{ route('reports.sales') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('reports.sales') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Sales Report</a>
                    @endif
                </div>
            </div>

            {{-- Finance: the P&L, with its balance sheet snapshot and AP/AR aging --}}
            @if (auth()->user()->hasPermission('dashboard.view'))
                <a href="{{ route('reports.index') }}"
                   class="flex items-center gap-3 rounded-md px-3 py-2 mb-0.5 {{ request()->routeIs('reports.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 17V9m4 8V5m4 12v-4M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Finance
                </a>
            @endif

            <a href="{{ route('batches.index') }}"
               class="flex items-center gap-3 rounded-md px-3 py-2 mb-0.5 {{ request()->routeIs('batches.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Batches
            </a>

            {{-- Leftover operational reports that don't belong to Purchase/Inventory/Sales/Finance --}}
            @if (auth()->user()->hasPermission('dashboard.view'))
                <div x-data="{ open: {{ $reportsActive ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="flex w-full items-center justify-between rounded-md px-3 py-2 mb-0.5 {{ $reportsActive ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                        <span class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Reports
                        </span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform shrink-0" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.14l3.71-3.91a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-show="open" x-cloak>
                        <a href="{{ route('reports.movements') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('reports.movements') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Animal Movements</a>
                        <a href="{{ route('reports.health') }}" class="flex items-center gap-3 rounded-md px-3 py-2 ml-2 mb-0.5 {{ request()->routeIs('reports.health') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">Health</a>
                    </div>
                </div>
            @endif

            @if (auth()->user()->hasPermission('user.manage'))
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 rounded-md px-3 py-2 mb-0.5 {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Users
                </a>
            @endif
        </nav>

        <div class="border-t border-gray-200 p-3 dark:border-gray-800">
            <div class="flex items-center justify-between rounded-md px-2 py-1.5">
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium">{{ auth()->user()->full_name }}</p>
                    <p class="truncate text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->role->name }}</p>
                </div>
                <button
                    @click="
                        document.documentElement.classList.toggle('dark');
                        localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                    "
                    class="shrink-0 rounded-md p-1.5 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800"
                    title="Toggle dark mode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.36 6.36l-.7-.7M6.34 6.34l-.7-.7m12.02 0l-.7.7M6.34 17.66l-.7.7M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button class="flex w-full items-center gap-2 rounded-md px-2 py-1.5 text-sm text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex h-16 items-center justify-between gap-3 border-b border-gray-200 bg-white px-4 dark:border-gray-800 dark:bg-gray-900 md:hidden">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="rounded-md p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <span class="font-semibold">Livestock ERP</span>
        </div>
        @include('partials.notification-bell')
    </div>

    <main class="min-h-screen px-4 py-6 sm:px-6 md:ml-64 md:px-8 md:py-8">
        @if (session('status'))
            <div class="mb-4 rounded-md bg-green-50 px-4 py-2 text-sm text-green-800 dark:bg-green-500/10 dark:text-green-300">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 px-4 py-2 text-sm text-red-800 dark:bg-red-500/10 dark:text-red-300">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
    @else
        <main class="min-h-screen">
            @yield('content')
        </main>
    @endauth
    @stack('scripts')
</body>
</html>
