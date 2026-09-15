@extends('layouts.app')

@section('title', 'Record a Sale')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Record a Sale</h1>

@if ($animals->isEmpty())
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 text-sm text-gray-500 dark:text-gray-400">
        No animals are currently on feed. Intake and grow an animal before recording a sale.
    </div>
@else
<form method="POST" action="{{ route('sales-orders.store') }}"
      x-data="{ rows: [{ animal_id: '', sale_weight_kg: '', price_per_kg: '' }] }"
      class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 space-y-6 max-w-3xl">
    @csrf

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Customer</label>
            <select name="customer_id" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="">Select customer</option>
                @foreach ($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                @endforeach
            </select>
            @if ($customers->isEmpty())
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">No customers yet — <a href="{{ route('customers.index') }}" class="underline">add one</a> first.</p>
            @endif
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sale Date</label>
            <input type="date" name="sale_date" value="{{ now()->format('Y-m-d') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <h2 class="font-semibold text-sm">Animals</h2>
            <button type="button" @click="rows.push({ animal_id: '', sale_weight_kg: '', price_per_kg: '' })"
                    class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">+ Add another animal</button>
        </div>

        <template x-for="(row, index) in rows" :key="index">
            <div class="grid grid-cols-12 gap-2 mb-2 items-center">
                <select :name="`items[${index}][animal_id]`" x-model="row.animal_id" required class="col-span-6 border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                    <option value="">Select animal</option>
                    @foreach ($animals as $animal)
                        <option value="{{ $animal->id }}">{{ $animal->tag_id }} — {{ $animal->species->name }} ({{ $animal->batch->batch_code }})</option>
                    @endforeach
                </select>
                <input :name="`items[${index}][sale_weight_kg]`" x-model="row.sale_weight_kg" type="number" step="0.01" placeholder="Weight (kg)" required class="col-span-3 border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                <input :name="`items[${index}][price_per_kg]`" x-model="row.price_per_kg" type="number" step="0.01" placeholder="Price/kg" required class="col-span-2 border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                <button type="button" @click="rows.splice(index, 1)" x-show="rows.length > 1" class="col-span-1 text-red-500 hover:text-red-700 text-sm">✕</button>
            </div>
        </template>
    </div>

    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Record Sale</button>
</form>
@endif
@endsection
