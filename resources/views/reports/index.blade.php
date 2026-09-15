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

<p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Accrual basis: revenue and cost of goods sold are recognized only when an animal actually sells. Animals still on feed carry their cost as work-in-progress inventory (below), not a loss.</p>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Realized Revenue</div>
        <div class="text-xl font-semibold">{{ number_format($totals['revenue'], 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">COGS + Overhead</div>
        <div class="text-xl font-semibold">{{ number_format($totals['cogs'] + $totals['overhead_expenses'], 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Mortality Loss</div>
        <div class="text-xl font-semibold {{ $totals['mortality_loss'] > 0 ? 'text-red-600 dark:text-red-400' : '' }}">{{ number_format($totals['mortality_loss'], 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Net Profit</div>
        <div class="text-xl font-semibold {{ $totals['net_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ number_format($totals['net_profit'], 2) }}</div>
    </div>
</div>

<h2 class="font-semibold mb-3">Balance Sheet Snapshot <span class="text-xs font-normal text-gray-500 dark:text-gray-400">(as of today, not the date range above)</span></h2>
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Livestock WIP Value</div>
        <div class="text-xl font-semibold">{{ number_format($wipValue, 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Feed Inventory Value</div>
        <div class="text-xl font-semibold">{{ number_format($feedInventoryValue, 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Accounts Receivable</div>
        <div class="text-xl font-semibold">{{ number_format($totalReceivable, 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Accounts Payable</div>
        <div class="text-xl font-semibold">{{ number_format($totalPayable, 2) }}</div>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden mb-6">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th class="px-4 py-2">Batch</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Revenue</th>
                <th class="px-4 py-2">COGS</th>
                <th class="px-4 py-2">Mortality</th>
                <th class="px-4 py-2">WIP Value</th>
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
                    <td class="px-4 py-2">{{ number_format($row['realized_revenue'], 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($row['realized_cogs'], 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($row['mortality_loss'], 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($row['wip_value'], 2) }}</td>
                    <td class="px-4 py-2 font-medium {{ $row['net_profit'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">{{ number_format($row['net_profit'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No batches started in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
        <h2 class="font-semibold p-4 pb-0">Accounts Receivable Aging</h2>
        <table class="w-full text-sm mt-3">
            <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <tr><th class="px-4 py-2">Customer</th><th class="px-4 py-2">Invoice</th><th class="px-4 py-2">Balance</th><th class="px-4 py-2">Age</th></tr>
            </thead>
            <tbody>
                @forelse ($accountsReceivable as $row)
                    <tr class="border-t border-gray-200 dark:border-gray-800">
                        <td class="px-4 py-2">{{ $row['party'] }}</td>
                        <td class="px-4 py-2">{{ $row['reference'] }}</td>
                        <td class="px-4 py-2">{{ number_format($row['balance'], 2) }}</td>
                        <td class="px-4 py-2 {{ $row['days'] > 60 ? 'text-red-600 dark:text-red-400' : '' }}">{{ $row['bucket'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Nothing outstanding.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
        <h2 class="font-semibold p-4 pb-0">Accounts Payable Aging</h2>
        <table class="w-full text-sm mt-3">
            <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                <tr><th class="px-4 py-2">Supplier</th><th class="px-4 py-2">PO</th><th class="px-4 py-2">Balance</th><th class="px-4 py-2">Age</th></tr>
            </thead>
            <tbody>
                @forelse ($accountsPayable as $row)
                    <tr class="border-t border-gray-200 dark:border-gray-800">
                        <td class="px-4 py-2">{{ $row['party'] }}</td>
                        <td class="px-4 py-2">{{ $row['reference'] }}</td>
                        <td class="px-4 py-2">{{ number_format($row['balance'], 2) }}</td>
                        <td class="px-4 py-2 {{ $row['days'] > 60 ? 'text-red-600 dark:text-red-400' : '' }}">{{ $row['bucket'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Nothing outstanding.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
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
