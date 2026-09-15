@extends('layouts.app')

@section('title', 'New Purchase Order')

@section('content')
<h1 class="text-2xl font-semibold mb-6">New Purchase Order</h1>

@if ($suppliers->isEmpty())
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 text-sm text-gray-500 dark:text-gray-400">
        No suppliers yet — <a href="{{ route('suppliers.index') }}" class="underline">add one</a> first.
    </div>
@else
<form method="POST" action="{{ route('purchase-orders.store') }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 max-w-lg space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Supplier</label>
        <select name="supplier_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">Select supplier</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Order Type</label>
        <select name="order_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="animal">Animal</option>
            <option value="feed">Feed</option>
            <option value="medicine">Medicine</option>
            <option value="other">Other</option>
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Order Date</label>
            <input type="date" name="order_date" value="{{ now()->format('Y-m-d') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Total Amount</label>
            <input type="number" step="0.01" name="total_amount" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
    </div>
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Create Purchase Order</button>
</form>
@endif
@endsection
