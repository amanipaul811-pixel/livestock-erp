@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-semibold">Profit &amp; Loss</h1>
    <div class="flex gap-2">
        <a href="{{ route('reports.export-pdf', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export PDF</a>
        <a href="{{ route('reports.export-excel', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export Excel</a>
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
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
</form>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Revenue</div>
        <div class="text-xl font-semibold">{{ number_format($totals['revenue'], 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Total Costs</div>
        <div class="text-xl font-semibold">{{ number_format($totals['purchase_cost'] + $totals['feed_cost'] + $totals['health_cost'] + $totals['other_expenses'] + $totals['overhead_expenses'], 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Overhead (unassigned)</div>
        <div class="text-xl font-semibold">{{ number_format($totals['overhead_expenses'], 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Net Profit</div>
        <div class="text-xl font-semibold {{ $totals['net_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ number_format($totals['net_profit'], 2) }}</div>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden mb-6">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th class="px-4 py-2">Batch</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Revenue</th>
                <th class="px-4 py-2">Purchase</th>
                <th class="px-4 py-2">Feed</th>
                <th class="px-4 py-2">Health</th>
                <th class="px-4 py-2">Other</th>
                <th class="px-4 py-2">Net Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($batchRows as $row)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2 font-medium">
                        <a href="{{ route('batches.show', $row['batch']) }}" class="hover:underline">{{ $row['batch']->batch_code }}</a>
                    </td>
                    <td class="px-4 py-2">{{ $row['batch']->status }}</td>
                    <td class="px-4 py-2">{{ number_format($row['revenue'], 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($row['purchase_cost'], 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($row['feed_cost'], 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($row['health_cost'], 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($row['other_expenses'], 2) }}</td>
                    <td class="px-4 py-2 font-medium {{ $row['net_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ number_format($row['net_profit'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No batches started in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Expenses by Category</h2>
        @forelse ($expenseByCategory as $category => $total)
            <div class="flex justify-between text-sm py-1 border-b border-gray-100 dark:border-gray-800 last:border-0">
                <span class="capitalize">{{ $category }}</span>
                <span class="font-medium">{{ number_format($total, 2) }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No expenses in this period.</p>
        @endforelse
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Revenue by Species</h2>
        @forelse ($revenueBySpecies as $species => $total)
            <div class="flex justify-between text-sm py-1 border-b border-gray-100 dark:border-gray-800 last:border-0">
                <span>{{ $species }}</span>
                <span class="font-medium">{{ number_format($total, 2) }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No sales in this period.</p>
        @endforelse
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 mt-6">
    <h2 class="font-semibold mb-3">Revenue by Customer</h2>
    @forelse ($revenueByCustomer as $customer => $total)
        <div class="flex justify-between text-sm py-1 border-b border-gray-100 dark:border-gray-800 last:border-0">
            <span>{{ $customer }}</span>
            <span class="font-medium">{{ number_format($total, 2) }}</span>
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">No sales in this period.</p>
    @endforelse
</div>
@endsection
