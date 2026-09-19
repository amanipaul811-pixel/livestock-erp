@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
<div class="flex items-center gap-3 mb-6">
    @include('partials.back-button', ['fallback' => route('sections.purchase')])
    <h1 class="text-2xl font-semibold">Suppliers</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Type</th><th class="px-4 py-2">Phone</th><th class="px-4 py-2"></th></tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr class="border-t border-gray-200 dark:border-gray-800">
                        <td class="px-4 py-2 font-medium">{{ $supplier->name }}</td>
                        <td class="px-4 py-2">{{ $supplier->supplier_type }}</td>
                        <td class="px-4 py-2">{{ $supplier->phone ?? '—' }}</td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            @if (auth()->user()->hasPermission('supplier.create'))
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Edit</a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" class="inline" onsubmit="return confirm('Delete this supplier?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-xs ml-2">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No suppliers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (auth()->user()->hasPermission('supplier.create'))
    <form method="POST" action="{{ route('suppliers.store') }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 space-y-3 h-fit">
        @csrf
        <h2 class="font-semibold">Add Supplier</h2>
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Type</label>
            <select name="supplier_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="animal">Animal</option>
                <option value="feed">Feed</option>
                <option value="medicine">Medicine</option>
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
    @endif
</div>
@endsection
