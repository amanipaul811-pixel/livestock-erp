@extends('layouts.app')

@section('title', 'Stock Movement Report')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-3">
        @include('partials.back-button', ['fallback' => route('sections.inventory')])
        <h1 class="text-2xl font-semibold">Stock Movement Report</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('reports.stock.export-pdf', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export PDF</a>
        <a href="{{ route('reports.stock.export-excel', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export Excel</a>
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
        <label class="block text-sm font-medium mb-1">Feed Item</label>
        <select name="feed_item_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">All feed items</option>
            @foreach ($feedItems as $item)
                <option value="{{ $item->id }}" @selected(request('feed_item_id') == $item->id)>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="type" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">Any type</option>
            @foreach (['in', 'out', 'adjustment'] as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
</form>

<p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Select a single feed item to see a running balance and current stock.</p>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Total In</div>
        <div class="text-xl font-semibold">{{ number_format($totalIn, 2) }} kg</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Total Out</div>
        <div class="text-xl font-semibold">{{ number_format($totalOut, 2) }} kg</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Net Adjustment</div>
        <div class="text-xl font-semibold">{{ number_format($totalAdjustment, 2) }} kg</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Current Stock</div>
        <div class="text-xl font-semibold">{{ $currentStock !== null ? number_format($currentStock, 2).' kg' : '—' }}</div>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th class="px-4 py-2">Date</th><th class="px-4 py-2">Feed Item</th><th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Quantity</th>
                @if ($runningBalance !== null)<th class="px-4 py-2">Balance After</th>@endif
                <th class="px-4 py-2">Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                @php $m = $row['movement']; @endphp
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2">{{ $m->occurred_at->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ $m->feedItem->name }}</td>
                    <td class="px-4 py-2 capitalize">
                        <span @class([
                            'px-2 py-0.5 rounded text-xs font-medium',
                            'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' => $m->type === 'in',
                            'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' => $m->type === 'out',
                            'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $m->type === 'adjustment',
                        ])>{{ $m->type }}</span>
                    </td>
                    <td class="px-4 py-2">{{ number_format($m->quantity_kg, 2) }} kg</td>
                    @if ($runningBalance !== null)<td class="px-4 py-2 font-medium">{{ number_format($row['balance'], 2) }} kg</td>@endif
                    <td class="px-4 py-2">{{ $m->reason ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No stock movements match this filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
