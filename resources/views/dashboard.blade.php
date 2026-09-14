@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Dashboard</h1>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded shadow p-4">
        <div class="text-sm text-gray-500">Active Batches</div>
        <div class="text-2xl font-semibold">{{ $activeBatches }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-sm text-gray-500">Animals On Feed</div>
        <div class="text-2xl font-semibold">{{ $animalsOnFeed }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-sm text-gray-500">Avg Daily Gain</div>
        <div class="text-2xl font-semibold">{{ $avgAdg }} kg</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-sm text-gray-500">Ready to Sell</div>
        <div class="text-2xl font-semibold">{{ $readyToSell->count() }}</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Species Breakdown (on feed)</h2>
        @forelse ($speciesBreakdown as $species => $count)
            <div class="flex justify-between text-sm py-1 border-b last:border-0">
                <span>{{ $species }}</span>
                <span class="font-medium">{{ $count }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500">No animals on feed yet.</p>
        @endforelse
    </div>

    <div class="bg-white rounded shadow p-4">
        <h2 class="font-semibold mb-3">Ready to Sell</h2>
        @forelse ($readyToSell as $animal)
            <a href="{{ route('animals.show', $animal) }}" class="flex justify-between text-sm py-1 border-b last:border-0 hover:text-blue-600">
                <span>{{ $animal->tag_id }} ({{ $animal->species->name }})</span>
                <span class="font-medium">{{ $animal->latestWeightKg() }} kg</span>
            </a>
        @empty
            <p class="text-sm text-gray-500">No animals have hit their target weight yet.</p>
        @endforelse
    </div>
</div>

<div class="mt-8 flex gap-3">
    <a href="{{ route('batches.create') }}" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">+ New Batch</a>
    <a href="{{ route('batches.index') }}" class="border text-sm px-4 py-2 rounded hover:bg-gray-100">View Batches</a>
    <a href="{{ route('sales-orders.create') }}" class="border text-sm px-4 py-2 rounded hover:bg-gray-100">Record a Sale</a>
</div>
@endsection
