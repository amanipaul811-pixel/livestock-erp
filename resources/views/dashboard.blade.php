@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Active Batches</div>
        <div class="text-2xl font-semibold">{{ $activeBatches }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Animals On Feed</div>
        <div class="text-2xl font-semibold">{{ $animalsOnFeed }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Avg Daily Gain</div>
        <div class="text-2xl font-semibold">{{ $avgAdg }} kg</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Ready to Sell</div>
        <div class="text-2xl font-semibold">{{ $readyToSell->count() }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Revenue vs Expenses</h2>
        <canvas id="revenueExpenseChart" height="220"></canvas>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Species Breakdown (on feed)</h2>
        @if ($speciesBreakdown->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No animals on feed yet.</p>
        @else
            <canvas id="speciesChart" height="220"></canvas>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Weight Gain Trend</h2>
        @if ($chartWeightTrend->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No weigh-ins recorded yet.</p>
        @else
            <canvas id="weightTrendChart" height="220"></canvas>
        @endif
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Batch Profitability</h2>
        @if ($chartBatchLabels->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">No batches yet.</p>
        @else
            <canvas id="batchProfitChart" height="220"></canvas>
        @endif
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 mb-8">
    <h2 class="font-semibold mb-3">Ready to Sell</h2>
    @forelse ($readyToSell as $animal)
        <a href="{{ route('animals.show', $animal) }}" class="flex justify-between text-sm py-1 border-b last:border-0 hover:text-indigo-600 dark:hover:text-indigo-400">
            <span>{{ $animal->tag_id }} ({{ $animal->species->name }})</span>
            <span class="font-medium">{{ $animal->latestWeightKg() }} kg</span>
        </a>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">No animals have hit their target weight yet.</p>
    @endforelse
</div>

<div class="flex gap-3">
    @if (auth()->user()->hasPermission('batch.create'))
        <a href="{{ route('batches.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">+ New Batch</a>
    @endif
    <a href="{{ route('batches.index') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">View Batches</a>
    @if (auth()->user()->hasPermission('salesorder.create'))
        <a href="{{ route('sales-orders.create') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Record a Sale</a>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const isDark = document.documentElement.classList.contains('dark');
        Chart.defaults.color = isDark ? '#9ca3af' : '#4b5563';
        Chart.defaults.borderColor = isDark ? '#374151' : '#e5e7eb';

        const palette = ['#4f46e5', '#16a34a', '#f59e0b', '#ec4899', '#0ea5e9', '#a855f7'];
        const monthLabels = @json($chartMonthLabels);

        const revenueExpenseEl = document.getElementById('revenueExpenseChart');
        if (revenueExpenseEl) {
            new Chart(revenueExpenseEl, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [
                        { label: 'Revenue', data: @json($chartRevenue), backgroundColor: '#4f46e5' },
                        { label: 'Expenses', data: @json($chartExpenses), backgroundColor: '#ef4444' },
                    ],
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
        }

        const speciesEl = document.getElementById('speciesChart');
        if (speciesEl) {
            new Chart(speciesEl, {
                type: 'doughnut',
                data: {
                    labels: @json($speciesBreakdown->keys()),
                    datasets: [{ data: @json($speciesBreakdown->values()), backgroundColor: palette }],
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
        }

        const weightTrendEl = document.getElementById('weightTrendChart');
        if (weightTrendEl) {
            const weightSeries = @json($chartWeightTrend);
            new Chart(weightTrendEl, {
                type: 'line',
                data: {
                    labels: monthLabels,
                    datasets: weightSeries.map((series, i) => ({
                        label: series.label,
                        data: series.data,
                        borderColor: palette[i % palette.length],
                        backgroundColor: 'transparent',
                        spanGaps: true,
                        tension: 0.3,
                    })),
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
            });
        }

        const batchProfitEl = document.getElementById('batchProfitChart');
        if (batchProfitEl) {
            const batchProfit = @json($chartBatchProfit);
            new Chart(batchProfitEl, {
                type: 'bar',
                data: {
                    labels: @json($chartBatchLabels),
                    datasets: [{
                        label: 'Net Profit',
                        data: batchProfit,
                        backgroundColor: batchProfit.map((v) => v >= 0 ? '#16a34a' : '#dc2626'),
                    }],
                },
                options: { responsive: true, indexAxis: 'y', plugins: { legend: { display: false } } },
            });
        }
    })();
</script>
@endpush
