@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit Supplier</h1>

<form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="bg-white rounded shadow p-6 max-w-md space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="supplier_type" required class="w-full border rounded px-3 py-2 text-sm">
            @foreach (['animal', 'feed', 'medicine', 'other'] as $type)
                <option value="{{ $type }}" @selected(old('supplier_type', $supplier->supplier_type) === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div class="flex gap-2">
        <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">Save</button>
        <a href="{{ route('suppliers.index') }}" class="border text-sm px-4 py-2 rounded hover:bg-gray-100">Cancel</a>
    </div>
</form>
@endsection
