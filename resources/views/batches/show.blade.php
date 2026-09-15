@extends('layouts.app')

@section('title', $batch->batch_code)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-start gap-3">
        @include('partials.back-button', ['fallback' => route('batches.index')])
        <div>
            <h1 class="text-2xl font-semibold">{{ $batch->batch_code }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $batch->species->name }} &middot; Pen: {{ $batch->pen->name ?? 'Unassigned' }} &middot; Status: {{ $batch->status }}</p>
        </div>
    </div>
    <div class="flex gap-2">
        @if (auth()->user()->hasPermission('batch.update'))
            <a href="{{ route('batches.edit', $batch) }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Edit</a>
        @endif
        @if (auth()->user()->hasPermission('animal.create'))
            <a href="{{ route('animals.create', $batch) }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">+ Intake Animal</a>
        @endif
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Head Count</div>
        <div class="text-xl font-semibold">{{ $kpis['head_count'] }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Days on Feed</div>
        <div class="text-xl font-semibold">{{ $kpis['days_on_feed'] }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">FCR</div>
        <div class="text-xl font-semibold">{{ $kpis['feed_conversion_ratio'] ?? '—' }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Net Profit</div>
        <div class="text-xl font-semibold {{ $kpis['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ number_format($kpis['net_profit'], 2) }}
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 mb-8 text-sm">
    <h2 class="font-semibold mb-3">Cost Breakdown</h2>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div><div class="text-gray-500 dark:text-gray-400 text-xs">Purchase</div>{{ number_format($kpis['total_purchase_cost'], 2) }}</div>
        <div><div class="text-gray-500 dark:text-gray-400 text-xs">Feed</div>{{ number_format($kpis['total_feed_cost'], 2) }}</div>
        <div><div class="text-gray-500 dark:text-gray-400 text-xs">Health</div>{{ number_format($kpis['total_health_cost'], 2) }}</div>
        <div><div class="text-gray-500 dark:text-gray-400 text-xs">Other</div>{{ number_format($kpis['total_other_expenses'], 2) }}</div>
        <div><div class="text-gray-500 dark:text-gray-400 text-xs">Revenue</div>{{ number_format($kpis['total_sales_revenue'], 2) }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Animals</h2>
        <table class="w-full text-sm">
            <thead class="text-left text-gray-500 dark:text-gray-400">
                <tr><th class="py-1">Tag</th><th class="py-1">Sex</th><th class="py-1">Entry Wt</th><th class="py-1">Status</th></tr>
            </thead>
            <tbody>
                @forelse ($batch->animals as $animal)
                    <tr class="border-t border-gray-200 hover:bg-gray-50 cursor-pointer dark:border-gray-800 dark:hover:bg-gray-800/60" onclick="window.location='{{ route('animals.show', $animal) }}'">
                        <td class="py-1.5">{{ $animal->tag_id }}</td>
                        <td class="py-1.5">{{ $animal->sex }}</td>
                        <td class="py-1.5">{{ $animal->entry_weight_kg }} kg</td>
                        <td class="py-1.5">{{ $animal->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500 dark:text-gray-400">No animals yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Feed Logs</h2>
        <table class="w-full text-sm mb-4">
            <thead class="text-left text-gray-500 dark:text-gray-400">
                <tr><th class="py-1">Date</th><th class="py-1">Feed</th><th class="py-1">Qty (kg)</th><th class="py-1">Cost</th></tr>
            </thead>
            <tbody>
                @forelse ($batch->feedLogs as $log)
                    <tr class="border-t border-gray-200 dark:border-gray-800">
                        <td class="py-1.5">{{ $log->feed_date->format('Y-m-d') }}</td>
                        <td class="py-1.5">{{ $log->feedItem->name }}</td>
                        <td class="py-1.5">{{ $log->quantity_kg }}</td>
                        <td class="py-1.5">{{ number_format($log->total_cost, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500 dark:text-gray-400">No feed logged yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if (auth()->user()->hasPermission('feedlog.create'))
            <form method="POST" action="{{ route('feed-logs.store', $batch) }}" class="border-t border-gray-200 dark:border-gray-800 pt-4 grid grid-cols-2 gap-2">
                @csrf
                <select name="feed_item_id" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800 col-span-2">
                    <option value="">Feed item</option>
                    @foreach ($feedItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }} @ {{ $item->cost_per_unit }})</option>
                    @endforeach
                </select>
                <input type="date" name="feed_date" value="{{ now()->format('Y-m-d') }}" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                <input type="number" step="0.01" name="quantity_kg" placeholder="Qty (kg)" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                <button type="submit" class="col-span-2 bg-indigo-600 text-white text-sm px-3 py-1.5 rounded-md hover:bg-indigo-700">Log Feed</button>
            </form>
            @if ($feedItems->isEmpty())
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">No feed items yet — <a href="{{ route('feed-items.index') }}" class="underline">add one</a> first.</p>
            @endif
        @endif
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 mt-6 max-w-xl">
    <h2 class="font-semibold mb-3">Other Expenses (overhead)</h2>
    <table class="w-full text-sm mb-4">
        <thead class="text-left text-gray-500 dark:text-gray-400">
            <tr><th class="py-1">Date</th><th class="py-1">Category</th><th class="py-1">Amount</th><th class="py-1">Description</th></tr>
        </thead>
        <tbody>
            @forelse ($batch->expenses as $expense)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="py-1.5">{{ $expense->expense_date->format('Y-m-d') }}</td>
                    <td class="py-1.5">{{ $expense->category }}</td>
                    <td class="py-1.5">{{ number_format($expense->amount, 2) }}</td>
                    <td class="py-1.5">{{ $expense->description ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-500 dark:text-gray-400">No expenses logged yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if (auth()->user()->hasPermission('expense.create'))
        <form method="POST" action="{{ route('expenses.store', $batch) }}" class="border-t border-gray-200 dark:border-gray-800 pt-4 grid grid-cols-2 gap-2">
            @csrf
            <select name="category" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="labor">Labor</option>
                <option value="utilities">Utilities</option>
                <option value="transport">Transport</option>
                <option value="rent">Rent</option>
                <option value="other">Other</option>
            </select>
            <input type="date" name="expense_date" value="{{ now()->format('Y-m-d') }}" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <input type="number" step="0.01" name="amount" placeholder="Amount" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <input type="text" name="description" placeholder="Description" class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <button type="submit" class="col-span-2 bg-indigo-600 text-white text-sm px-3 py-1.5 rounded-md hover:bg-indigo-700">Add Expense</button>
        </form>
    @endif
</div>
@endsection
