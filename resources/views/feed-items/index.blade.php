@extends('layouts.app')

@section('title', 'Feed Items')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Feed Items</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left text-gray-600">
                <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Unit</th><th class="px-4 py-2">Cost/Unit</th><th class="px-4 py-2">Warehouse</th></tr>
            </thead>
            <tbody>
                @forelse ($feedItems as $item)
                    <tr class="border-t">
                        <td class="px-4 py-2 font-medium">{{ $item->name }}</td>
                        <td class="px-4 py-2">{{ $item->unit }}</td>
                        <td class="px-4 py-2">{{ number_format($item->cost_per_unit, 2) }}</td>
                        <td class="px-4 py-2">{{ $item->warehouse->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">No feed items yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form method="POST" action="{{ route('feed-items.store') }}" class="bg-white rounded shadow p-4 space-y-3 h-fit">
        @csrf
        <h2 class="font-semibold">Add Feed Item</h2>
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" required placeholder="e.g. Maize Silage" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Unit</label>
            <select name="unit" required class="w-full border rounded px-3 py-2 text-sm">
                <option value="kg">kg</option>
                <option value="bag">bag</option>
                <option value="liter">liter</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Cost per Unit</label>
            <input type="number" step="0.01" name="cost_per_unit" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Warehouse</label>
            <select name="warehouse_id" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">None</option>
                @foreach ($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="w-full bg-gray-900 text-white text-sm px-3 py-2 rounded hover:bg-gray-700">Add</button>
    </form>
</div>
@endsection
