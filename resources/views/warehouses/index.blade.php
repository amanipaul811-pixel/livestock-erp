@extends('layouts.app')

@section('title', 'Warehouses')

@section('content')
<div class="flex items-center gap-3 mb-6">
    @include('partials.back-button', ['fallback' => route('sections.inventory')])
    <h1 class="text-2xl font-semibold">Warehouses</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Type</th><th class="px-4 py-2">Location</th><th class="px-4 py-2"></th></tr>
            </thead>
            <tbody>
                @forelse ($warehouses as $warehouse)
                    <tr class="border-t border-gray-200 dark:border-gray-800">
                        <td class="px-4 py-2 font-medium">{{ $warehouse->name }}</td>
                        <td class="px-4 py-2">{{ $warehouse->type }}</td>
                        <td class="px-4 py-2">{{ $warehouse->location ?? '—' }}</td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            @if (auth()->user()->hasPermission('warehouse.create'))
                                <a href="{{ route('warehouses.edit', $warehouse) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Edit</a>
                                <form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}" class="inline" onsubmit="return confirm('Delete this warehouse?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-xs ml-2">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No warehouses yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (auth()->user()->hasPermission('warehouse.create'))
    <form method="POST" action="{{ route('warehouses.store') }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 space-y-3 h-fit">
        @csrf
        <h2 class="font-semibold">Add Warehouse</h2>
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Type</label>
            <select name="type" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="feed">Feed</option>
                <option value="medicine">Medicine</option>
                <option value="equipment">Equipment</option>
                <option value="general">General</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Location</label>
            <input type="text" name="location" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white text-sm px-3 py-2 rounded-md hover:bg-indigo-700">Add</button>
    </form>
    @endif
</div>
@endsection
