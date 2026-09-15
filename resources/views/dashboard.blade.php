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

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Species Breakdown (on feed)</h2>
        @forelse ($speciesBreakdown as $species => $count)
            <div class="flex justify-between text-sm py-1 border-b last:border-0">
                <span>{{ $species }}</span>
                <span class="font-medium">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 dark:text-gray-400">No animals on feed yet.</p>
        @endforelse
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
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
</div>

<div class="mt-8 flex gap-3">
    @if (auth()->user()->hasPermission('batch.create'))
        <a href="{{ route('batches.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">+ New Batch</a>
    @endif
    <a href="{{ route('batches.index') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">View Batches</a>
    @if (auth()->user()->hasPermission('salesorder.create'))
        <a href="{{ route('sales-orders.create') }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Record a Sale</a>
    @endif
</div>
@endsection
