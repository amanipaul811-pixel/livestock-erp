@extends('layouts.app')

@section('title', 'Sales Report')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-semibold">Sales Report</h1>
    <div class="flex gap-2">
        <a href="{{ route('reports.sales.export-pdf', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export PDF</a>
        <a href="{{ route('reports.sales.export-excel', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export Excel</a>
    </div>
</div>

<form method="GET" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label class="block text-sm font-medium mb-1">From</label>
        <input type="date" name="from" value="{{ $from }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">To</label>
        <input type="date" name="to" value="{{ $to }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Customer</label>
        <select name="customer_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">All customers</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Species</label>
        <select name="species_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">All species</option>
            @foreach ($speciesList as $species)
                <option value="{{ $species->id }}" @selected(request('species_id') == $species->id)>{{ $species->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">Any status</option>
            @foreach (['pending', 'completed', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
</form>

<div class="grid grid-cols-2 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Animals Sold</div>
        <div class="text-xl font-semibold">{{ $totalAnimals }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</div>
        <div class="text-xl font-semibold">{{ number_format($totalRevenue, 2) }}</div>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th class="px-4 py-2">SO #</th><th class="px-4 py-2">Date</th><th class="px-4 py-2">Customer</th>
                <th class="px-4 py-2">Tag</th><th class="px-4 py-2">Species</th><th class="px-4 py-2">Weight</th>
                <th class="px-4 py-2">Price/kg</th><th class="px-4 py-2">Line Total</th><th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2">{{ $row->so_number }}</td>
                    <td class="px-4 py-2">{{ $row->sale_date }}</td>
                    <td class="px-4 py-2">{{ $row->customer }}</td>
                    <td class="px-4 py-2">{{ $row->tag_id }}</td>
                    <td class="px-4 py-2">{{ $row->species }}</td>
                    <td class="px-4 py-2">{{ number_format($row->sale_weight_kg, 2) }} kg</td>
                    <td class="px-4 py-2">{{ number_format($row->price_per_kg, 2) }}</td>
                    <td class="px-4 py-2 font-medium">{{ number_format($row->line_total, 2) }}</td>
                    <td class="px-4 py-2 capitalize">{{ $row->status }}</td>
                </tr>
            @empty
                <tr><td colspan="9" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No sales match this filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
