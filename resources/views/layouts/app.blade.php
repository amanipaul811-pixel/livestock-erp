<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Livestock ERP')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    @auth
    <nav class="bg-gray-900 text-white">
        <div class="max-w-6xl mx-auto px-4 flex items-center justify-between h-14">
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="font-semibold">Livestock ERP</a>
                <a href="{{ route('batches.index') }}" class="text-sm text-gray-300 hover:text-white">Batches</a>
                <a href="{{ route('feed-items.index') }}" class="text-sm text-gray-300 hover:text-white">Feed Items</a>
                <a href="{{ route('customers.index') }}" class="text-sm text-gray-300 hover:text-white">Customers</a>
                <a href="{{ route('sales-orders.create') }}" class="text-sm text-gray-300 hover:text-white">Sell</a>
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
