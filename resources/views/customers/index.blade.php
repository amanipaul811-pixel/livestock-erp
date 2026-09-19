@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="flex items-center gap-3 mb-6">
    @include('partials.back-button', ['fallback' => route('sections.sales')])
    <h1 class="text-2xl font-semibold">Customers</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Type</th><th class="px-4 py-2">Phone</th><th class="px-4 py-2"></th></tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr class="border-t border-gray-200 dark:border-gray-800">
                        <td class="px-4 py-2 font-medium">{{ $customer->name }}</td>
                        <td class="px-4 py-2">{{ $customer->customer_type }}</td>
                        <td class="px-4 py-2">{{ $customer->phone ?? '—' }}</td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            <a href="{{ route('customers.edit', $customer) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Edit</a>
                            <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('Delete this customer?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-xs ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No customers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form method="POST" action="{{ route('customers.store') }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 space-y-3 h-fit">
        @csrf
        <h2 class="font-semibold">Add Customer</h2>
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Type</label>
            <select name="customer_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="individual">Individual</option>
                <option value="butcher">Butcher</option>
                <option value="trader">Trader</option>
                <option value="exporter">Exporter</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Phone</label>
            <input type="text" name="phone" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white text-sm px-3 py-2 rounded-md hover:bg-indigo-700">Add</button>
    </form>
</div>
@endsection
