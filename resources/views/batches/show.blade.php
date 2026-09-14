@extends('layouts.app')

@section('title', $batch->batch_code)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ $batch->batch_code }}</h1>
        <p class="text-sm text-gray-500">{{ $batch->species->name }} &middot; Pen: {{ $batch->pen->name ?? 'Unassigned' }} &middot; Status: {{ $batch->status }}</p>
    </div>
    <a href="{{ route('animals.create', $batch) }}" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">+ Intake Animal</a>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs text-gray-500">Head Count</div>
        <div class="text-xl font-semibold">{{ $kpis['head_count'] }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs text-gray-500">Days on Feed</div>
        <div class="text-xl font-semibold">{{ $kpis['days_on_feed'] }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs text-gray-500">FCR</div>
        <div class="text-xl font-semibold">{{ $kpis['feed_conversion_ratio'] ?? '—' }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs text-gray-500">Net Profit</div>
        <div class="text-xl font-semibold {{ $kpis['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
            {{ number_format($kpis['net_profit'], 2) }}
        </div>
    </div>
</div>

<div class="bg-white rounded shadow p-4 mb-8 text-sm">
    <h2 class="font-semibold mb-3">Cost Breakdown</h2>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div><div class="text-gray-500 text-xs">Purchase</div>{{ number_format($kpis['total_purchase_cost'], 2) }}</div>
        <div><div class="text-gray-500 text-xs">Feed</div>{{ number_format($kpis['total_feed_cost'], 2) }}</div>
        <div><div class="text-gray-500 text-xs">Health</div>{{ number_format($kpis['total_health_cost'], 2) }}</div>
        <div><div class="text-gray-500 text-xs">Other</div>{{ number_format($kpis['total_other_expenses'], 2) }}</div>
        <div><div class="text-gray-500 text-xs">Revenue</div>{{ number_format($kpis['total_sales_revenue'], 2) }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Animals</h2>
        <table class="w-full text-sm">
            <thead class="text-left text-gray-500">
                <tr><th class="py-1">Tag</th><th class="py-1">Sex</th><th class="py-1">Entry Wt</th><th class="py-1">Status</th></tr>
            </thead>
            <tbody>
                @forelse ($batch->animals as $animal)
                    <tr class="border-t hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('animals.show', $animal) }}'">
                        <td class="py-1.5">{{ $animal->tag_id }}</td>
                        <td class="py-1.5">{{ $animal->sex }}</td>
                        <td class="py-1.5">{{ $animal->entry_weight_kg }} kg</td>
                        <td class="py-1.5">{{ $animal->status }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500">No animals yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Feed Logs</h2>
        <table class="w-full text-sm mb-4">
            <thead class="text-left text-gray-500">
                <tr><th class="py-1">Date</th><th class="py-1">Feed</th><th class="py-1">Qty (kg)</th><th class="py-1">Cost</th></tr>
            </thead>
            <tbody>
                @forelse ($batch->feedLogs as $log)
                    <tr class="border-t">
                        <td class="py-1.5">{{ $log->feed_date->format('Y-m-d') }}</td>
                        <td class="py-1.5">{{ $log->feedItem->name }}</td>
                        <td class="py-1.5">{{ $log->quantity_kg }}</td>
                        <td class="py-1.5">{{ number_format($log->total_cost, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-4 text-center text-gray-500">No feed logged yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <form method="POST" action="{{ route('feed-logs.store', $batch) }}" class="border-t pt-4 grid grid-cols-2 gap-2">
            @csrf
            <select name="feed_item_id" required class="border rounded px-2 py-1.5 text-sm col-span-2">
                <option value="">Feed item</option>
                @foreach ($feedItems as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }} @ {{ $item->cost_per_unit }})</option>
                @endforeach
            </select>
            <input type="date" name="feed_date" value="{{ now()->format('Y-m-d') }}" required class="border rounded px-2 py-1.5 text-sm">
            <input type="number" step="0.01" name="quantity_kg" placeholder="Qty (kg)" required class="border rounded px-2 py-1.5 text-sm">
            <button type="submit" class="col-span-2 bg-gray-900 text-white text-sm px-3 py-1.5 rounded hover:bg-gray-700">Log Feed</button>
        </form>
        @if ($feedItems->isEmpty())
            <p class="text-xs text-gray-500 mt-2">No feed items yet — <a href="{{ route('feed-items.index') }}" class="underline">add one</a> first.</p>
        @endif
    </div>
</div>
@endsection
