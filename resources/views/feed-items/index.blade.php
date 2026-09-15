@extends('layouts.app')

@section('title', 'Feed Items')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Feed Items</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Unit</th><th class="px-4 py-2">Cost/Unit</th><th class="px-4 py-2">Warehouse</th><th class="px-4 py-2">Stock</th><th class="px-4 py-2"></th></tr>
            </thead>
            <tbody>
                @forelse ($feedItems as $item)
                    @php $lowStock = $item->isLowStock(); @endphp
                    <tr class="border-t border-gray-200 dark:border-gray-800 {{ $lowStock ? 'bg-red-50 dark:bg-red-500/10' : '' }}">
                        <td class="px-4 py-2 font-medium">{{ $item->name }}</td>
                        <td class="px-4 py-2">{{ $item->unit }}</td>
                        <td class="px-4 py-2">{{ number_format($item->cost_per_unit, 2) }}</td>
                        <td class="px-4 py-2">{{ $item->warehouse->name ?? '—' }}</td>
                        <td class="px-4 py-2">
                            <span class="{{ $lowStock ? 'text-red-600 dark:text-red-400 font-semibold' : '' }}">{{ number_format($item->currentStock(), 1) }} kg</span>
                            @if ($lowStock)
                                <span class="ml-1 inline-block px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400">Low</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-right whitespace-nowrap">
                            <a href="{{ route('feed-items.edit', $item) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Edit</a>
                            <button type="button" x-data @click="$dispatch('open-restock', { id: {{ $item->id }}, name: {{ Js::from($item->name) }} })" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs ml-2">Restock</button>
                            <form method="POST" action="{{ route('feed-items.destroy', $item) }}" class="inline" onsubmit="return confirm('Delete this feed item?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:underline text-xs ml-2">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No feed items yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form method="POST" action="{{ route('feed-items.store') }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 space-y-3 h-fit">
        @csrf
        <h2 class="font-semibold">Add Feed Item</h2>
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" required placeholder="e.g. Maize Silage" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Unit</label>
            <select name="unit" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="kg">kg</option>
                <option value="bag">bag</option>
                <option value="liter">liter</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Cost per Unit</label>
            <input type="number" step="0.01" name="cost_per_unit" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Warehouse</label>
            <select name="warehouse_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="">None</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Reorder Level (kg)</label>
            <input type="number" step="0.01" name="reorder_level" placeholder="0" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white text-sm px-3 py-2 rounded-md hover:bg-indigo-700">Add</button>
    </form>
</div>

<div x-data="{ open: false, feedItemId: null, feedItemName: '' }"
     @open-restock.window="open = true; feedItemId = $event.detail.id; feedItemName = $event.detail.name"
     x-show="open" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
    <div @click.outside="open = false" class="w-full max-w-sm bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Restock <span x-text="feedItemName"></span></h2>
        <form method="POST" :action="`/feed-items/${feedItemId}/restock`" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">Quantity (kg)</label>
                <input type="number" step="0.01" name="quantity_kg" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Reason (optional)</label>
                <input type="text" name="reason" placeholder="e.g. Supplier delivery" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Add Stock</button>
                <button type="button" @click="open = false" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</button>
            </div>
        </form>
    </div>
</div>
@endsection
