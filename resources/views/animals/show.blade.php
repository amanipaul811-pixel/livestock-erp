@extends('layouts.app')

@section('title', $animal->tag_id)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ $animal->tag_id }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ $animal->species->name }} &middot; {{ $animal->sex }} &middot;
            Batch: <a href="{{ route('batches.show', $animal->batch) }}" class="underline">{{ $animal->batch->batch_code }}</a>
            &middot; Pen: {{ $animal->currentPen->name ?? 'Unassigned' }}
            &middot; Status: {{ $animal->status }}
        </p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('animals.tag', $animal) }}" target="_blank" class="flex items-center gap-2 border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            <img src="{{ route('animals.qr-code', $animal) }}" alt="QR code" class="h-8 w-8">
            Print Tag
        </a>
        @if ($animal->status === 'on_feed' && auth()->user()->hasPermission('salesorder.create'))
            <a href="{{ route('sales-orders.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Sell</a>
        @endif
    </div>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Entry Weight</div>
        <div class="text-xl font-semibold">{{ $animal->entry_weight_kg }} kg</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Latest Weight</div>
        <div class="text-xl font-semibold">{{ $latestWeight }} kg</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Avg Daily Gain</div>
        <div class="text-xl font-semibold">{{ $adg }} kg/day</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Ready to Sell</div>
        <div class="text-xl font-semibold {{ $readyToSell ? 'text-green-600' : 'text-gray-400' }}">
            {{ $readyToSell ? 'Yes' : 'Not yet' }}
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Weigh-ins</h2>
        <table class="w-full text-sm mb-4">
            <thead class="text-left text-gray-500 dark:text-gray-400"><tr><th class="py-1">Date</th><th class="py-1">Weight (kg)</th></tr></thead>
            <tbody>
                @forelse ($animal->weighIns as $w)
                    <tr class="border-t border-gray-200 dark:border-gray-800"><td class="py-1.5">{{ $w->weigh_date->format('Y-m-d') }}</td><td class="py-1.5">{{ $w->weight_kg }}</td></tr>
                @empty
                    <tr><td colspan="2" class="py-4 text-center text-gray-500 dark:text-gray-400">No weigh-ins yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($animal->status === 'on_feed' && auth()->user()->hasPermission('weighin.create'))
        <form method="POST" action="{{ route('weigh-ins.store', $animal) }}" class="border-t border-gray-200 dark:border-gray-800 pt-4 grid grid-cols-2 gap-2">
            @csrf
            <input type="date" name="weigh_date" value="{{ now()->format('Y-m-d') }}" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <input type="number" step="0.01" name="weight_kg" placeholder="Weight (kg)" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <button type="submit" class="col-span-2 bg-indigo-600 text-white text-sm px-3 py-1.5 rounded-md hover:bg-indigo-700">Record Weigh-in</button>
        </form>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <h2 class="font-semibold mb-3">Health Records</h2>
        <table class="w-full text-sm mb-4">
            <thead class="text-left text-gray-500 dark:text-gray-400"><tr><th class="py-1">Date</th><th class="py-1">Type</th><th class="py-1">Cost</th></tr></thead>
            <tbody>
                @forelse ($animal->healthRecords as $h)
                    <tr class="border-t border-gray-200 dark:border-gray-800"><td class="py-1.5">{{ $h->record_date->format('Y-m-d') }}</td><td class="py-1.5">{{ $h->record_type }}</td><td class="py-1.5">{{ number_format($h->cost, 2) }}</td></tr>
                @empty
                    <tr><td colspan="3" class="py-4 text-center text-gray-500 dark:text-gray-400">No health records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if ($animal->status !== 'sold' && auth()->user()->hasPermission('healthrecord.create'))
        <form method="POST" action="{{ route('health-records.store', $animal) }}" x-data="{ type: 'vaccination' }" class="border-t border-gray-200 dark:border-gray-800 pt-4 grid grid-cols-2 gap-2">
            @csrf
            <select name="record_type" x-model="type" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800 col-span-2">
                <option value="vaccination">Vaccination</option>
                <option value="deworming">Deworming</option>
                <option value="treatment">Treatment</option>
                <option value="checkup">Checkup</option>
                <option value="death">Death</option>
            </select>
            <input type="date" name="record_date" value="{{ now()->format('Y-m-d') }}" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <input type="number" step="0.01" name="cost" placeholder="Cost" class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <input type="text" name="description" placeholder="Description" class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800 col-span-2">
            <input type="text" name="cause_of_death" placeholder="Cause of death" x-show="type === 'death'" class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800 col-span-2">
            <button type="submit" class="col-span-2 bg-indigo-600 text-white text-sm px-3 py-1.5 rounded-md hover:bg-indigo-700">Add Health Record</button>
        </form>
        @endif
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 mt-6 max-w-xl">
    <h2 class="font-semibold mb-3">Pen Movements</h2>
    <table class="w-full text-sm mb-4">
        <thead class="text-left text-gray-500 dark:text-gray-400">
            <tr><th class="py-1">Date</th><th class="py-1">From</th><th class="py-1">To</th><th class="py-1">Reason</th></tr>
        </thead>
        <tbody>
            @forelse ($animal->movements as $movement)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="py-1.5">{{ $movement->move_date->format('Y-m-d') }}</td>
                    <td class="py-1.5">{{ $movement->fromPen->name ?? '—' }}</td>
                    <td class="py-1.5">{{ $movement->toPen->name }}</td>
                    <td class="py-1.5">{{ $movement->reason ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="py-4 text-center text-gray-500 dark:text-gray-400">No pen transfers yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($animal->status === 'on_feed' && auth()->user()->hasPermission('animalmovement.create'))
        <form method="POST" action="{{ route('movements.store', $animal) }}" class="border-t border-gray-200 dark:border-gray-800 pt-4 grid grid-cols-2 gap-2">
            @csrf
            <select name="to_pen_id" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="">Move to pen</option>
                @foreach ($pens as $pen)
                    <option value="{{ $pen->id }}" @disabled($pen->id === $animal->current_pen_id)>{{ $pen->name }} ({{ $pen->stage }})</option>
                @endforeach
            </select>
            <input type="date" name="move_date" value="{{ now()->format('Y-m-d') }}" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <input type="text" name="reason" placeholder="Reason (optional)" class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800 col-span-2">
            <button type="submit" class="col-span-2 bg-indigo-600 text-white text-sm px-3 py-1.5 rounded-md hover:bg-indigo-700">Move Animal</button>
        </form>
    @endif
</div>
@endsection
