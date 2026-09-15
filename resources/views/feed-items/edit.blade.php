@extends('layouts.app')

@section('title', 'Edit Feed Item')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit Feed Item</h1>

<form method="POST" action="{{ route('feed-items.update', $feedItem) }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 max-w-md space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $feedItem->name) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Unit</label>
        <select name="unit" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            @foreach (['kg', 'bag', 'liter'] as $unit)
                <option value="{{ $unit }}" @selected(old('unit', $feedItem->unit) === $unit)>{{ $unit }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Cost per Unit</label>
        <input type="number" step="0.01" name="cost_per_unit" value="{{ old('cost_per_unit', $feedItem->cost_per_unit) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Warehouse</label>
        <select name="warehouse_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">None</option>
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(old('warehouse_id', $feedItem->warehouse_id) == $warehouse->id)>{{ $warehouse->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Save</button>
        <a href="{{ route('feed-items.index') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</a>
    </div>
</form>
@endsection
