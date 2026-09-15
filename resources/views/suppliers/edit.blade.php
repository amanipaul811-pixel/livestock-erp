@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit Supplier</h1>

<form method="POST" action="{{ route('suppliers.update', $supplier) }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 max-w-md space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $supplier->name) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="supplier_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            @foreach (['animal', 'feed', 'medicine', 'other'] as $type)
                <option value="{{ $type }}" @selected(old('supplier_type', $supplier->supplier_type) === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div class="flex gap-2">
        <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Save</button>
        <a href="{{ route('suppliers.index') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</a>
    </div>
</form>
@endsection
