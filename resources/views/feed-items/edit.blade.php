@extends('layouts.app')

@section('title', 'Edit Feed Item')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit Feed Item</h1>

<form method="POST" action="{{ route('feed-items.update', $feedItem) }}" class="bg-white rounded shadow p-6 max-w-md space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $feedItem->name) }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Unit</label>
        <select name="unit" required class="w-full border rounded px-3 py-2 text-sm">
            @foreach (['kg', 'bag', 'liter'] as $unit)
                <option value="{{ $unit }}" @selected(old('unit', $feedItem->unit) === $unit)>{{ $unit }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Cost per Unit</label>
        <input type="number" step="0.01" name="cost_per_unit" value="{{ old('cost_per_unit', $feedItem->cost_per_unit) }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Warehouse</label>
        <select name="warehouse_id" class="w-full border rounded px-3 py-2 text-sm">
            <option value="">None</option>
            @foreach ($warehouses as $warehouse)
                <option value="{{ $warehouse->id }}" @selected(old('warehouse_id', $feedItem->warehouse_id) == $warehouse->id)>{{ $warehouse->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">Save</button>
        <a href="{{ route('feed-items.index') }}" class="border text-sm px-4 py-2 rounded hover:bg-gray-100">Cancel</a>
    </div>
</form>
@endsection
