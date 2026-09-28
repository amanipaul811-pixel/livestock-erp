@extends('layouts.app')

@section('title', 'New Purchase Order')

@section('content')
<div class="flex items-center gap-3 mb-6">
    @include('partials.back-button', ['fallback' => route('purchase-orders.index')])
    <h1 class="text-2xl font-semibold">New Purchase Order</h1>
</div>

@if ($suppliers->isEmpty())
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 text-sm text-gray-500 dark:text-gray-400">
        No suppliers yet — <a href="{{ route('suppliers.index') }}" class="underline">add one</a> first.
    </div>
@else
<form method="POST" action="{{ route('purchase-orders.store') }}" x-data="{ orderType: 'animal' }" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 max-w-3xl space-y-6">
    @csrf

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
            <select name="order_type" x-model="orderType" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="animal">Animal</option>
                <option value="feed">Feed</option>
                <option value="medicine">Medicine</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Order Date</label>
            <input type="date" name="order_date" value="{{ now()->format('Y-m-d') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
    </div>

    <div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                    <tr>
                        <th class="px-3 py-2 font-medium">Item</th>
                        <th class="px-3 py-2 font-medium w-40">Quantity</th>
                        <th class="px-3 py-2 font-medium w-48">Total Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-gray-200 dark:border-gray-800">
                        <td class="px-3 py-2 align-top">
                            <select x-show="orderType === 'animal'" x-cloak name="species_id" class="w-full border-0 bg-transparent text-sm focus:ring-0 p-0">
                                <option value="">Select species</option>
                                @foreach ($speciesList as $species)
                                    <option value="{{ $species->id }}">{{ $species->name }}</option>
                                @endforeach
                            </select>
                            <select x-show="orderType === 'feed'" x-cloak name="feed_item_id" class="w-full border-0 bg-transparent text-sm focus:ring-0 p-0">
                                <option value="">Select feed item</option>
                                @foreach ($feedItems as $feedItem)
                                    <option value="{{ $feedItem->id }}">{{ $feedItem->name }}</option>
                                @endforeach
                            </select>
                            <span x-show="orderType === 'medicine' || orderType === 'other'" x-cloak class="text-gray-400 dark:text-gray-500">No item</span>
                        </td>
                        <td class="px-3 py-2 align-top">
                            <input x-show="orderType === 'animal'" x-cloak type="number" step="1" min="1" name="quantity" placeholder="Head count" class="w-full border-0 bg-transparent text-sm focus:ring-0 p-0">
                            <input x-show="orderType === 'feed'" x-cloak type="number" step="0.01" name="quantity_kg" placeholder="kg" class="w-full border-0 bg-transparent text-sm focus:ring-0 p-0">
                            <span x-show="orderType === 'medicine' || orderType === 'other'" x-cloak class="text-gray-400 dark:text-gray-500">—</span>
                        </td>
                        <td class="px-3 py-2 align-top">
                            <input type="number" step="0.01" name="total_amount" required class="w-full border-0 bg-transparent text-sm focus:ring-0 p-0">
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
        <p x-show="orderType === 'feed'" x-cloak class="text-xs text-gray-500 dark:text-gray-400 mt-2">Marking this order received will add this quantity straight to feed stock.</p>
        <p x-show="orderType === 'animal'" x-cloak class="text-xs text-gray-500 dark:text-gray-400 mt-2">Marking this order received will let you record each animal against it (tag, weight, and the rest) until this head count is reached.</p>
        <p x-show="orderType === 'medicine' || orderType === 'other'" x-cloak class="text-xs text-gray-500 dark:text-gray-400 mt-2">Medicine and other orders aren't itemized — just enter the total amount.</p>
    </div>

    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Create Purchase Order</button>
</form>
@endif
@endsection
