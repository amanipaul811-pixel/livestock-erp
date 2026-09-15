@extends('layouts.app')

@section('title', 'Edit Warehouse')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit Warehouse</h1>

<form method="POST" action="{{ route('warehouses.update', $warehouse) }}" class="bg-white rounded shadow p-6 max-w-md space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $warehouse->name) }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="type" required class="w-full border rounded px-3 py-2 text-sm">
            @foreach (['feed', 'medicine', 'equipment', 'general'] as $type)
                <option value="{{ $type }}" @selected(old('type', $warehouse->type) === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Location</label>
        <input type="text" name="location" value="{{ old('location', $warehouse->location) }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div class="flex gap-2">
        <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">Save</button>
        <a href="{{ route('warehouses.index') }}" class="border text-sm px-4 py-2 rounded hover:bg-gray-100">Cancel</a>
    </div>
</form>
@endsection
