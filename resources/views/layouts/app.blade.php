<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Livestock ERP')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50 text-gray-900">
    @auth
    <nav class="bg-gray-900 text-white relative z-10">
        <div class="max-w-6xl mx-auto px-4 flex flex-wrap items-center justify-between gap-y-2 py-2 min-h-14">
            <div class="flex flex-wrap items-center gap-x-5 gap-y-1">
                <a href="{{ route('dashboard') }}" class="font-semibold">Livestock ERP</a>
                <a href="{{ route('batches.index') }}" class="text-sm text-gray-300 hover:text-white">Batches</a>
                <a href="{{ route('sales-orders.create') }}" class="text-sm text-gray-300 hover:text-white">Sell</a>
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button @click="open = !open" class="text-sm text-gray-300 hover:text-white flex items-center gap-1">
                        Setup
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.14l3.71-3.91a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                    </button>
                    <div x-show="open" x-cloak class="absolute left-0 mt-2 w-44 bg-white text-gray-800 rounded shadow-lg text-sm py-1">
                        <a href="{{ route('feed-items.index') }}" class="block px-4 py-2 hover:bg-gray-100">Feed Items</a>
                        <a href="{{ route('customers.index') }}" class="block px-4 py-2 hover:bg-gray-100">Customers</a>
                        <a href="{{ route('suppliers.index') }}" class="block px-4 py-2 hover:bg-gray-100">Suppliers</a>
                        <a href="{{ route('warehouses.index') }}" class="block px-4 py-2 hover:bg-gray-100">Warehouses</a>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <span class="text-gray-400">{{ auth()->user()->full_name }} ({{ auth()->user()->role->name }})</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-gray-300 hover:text-white">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <main class="max-w-6xl mx-auto px-4 py-8">
        @if (session('status'))
            <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2 text-sm">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 text-red-800 px-4 py-2 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
