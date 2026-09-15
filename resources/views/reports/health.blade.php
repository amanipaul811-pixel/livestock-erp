@extends('layouts.app')

@section('title', 'Health Report')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-semibold">Health Report</h1>
    <div class="flex gap-2">
        <a href="{{ route('reports.health.export-pdf', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export PDF</a>
        <a href="{{ route('reports.health.export-excel', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export Excel</a>
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
        <label class="block text-sm font-medium mb-1">Batch</label>
        <select name="batch_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">All batches</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" @selected(request('batch_id') == $batch->id)>{{ $batch->batch_code }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Tag</label>
        <input type="text" name="tag" value="{{ request('tag') }}" placeholder="e.g. CTL-1002" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="record_type" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">Any type</option>
            @foreach (['vaccination', 'deworming', 'treatment', 'checkup', 'death'] as $type)
                <option value="{{ $type }}" @selected(request('record_type') === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
</form>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Records</div>
        <div class="text-xl font-semibold">{{ $rows->count() }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Total Cost</div>
        <div class="text-xl font-semibold">{{ number_format($totalCost, 2) }}</div>
    </div>
    @foreach (['vaccination', 'treatment', 'death'] as $type)
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
            <div class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ $type }}</div>
            <div class="text-xl font-semibold">{{ $countByType[$type] ?? 0 }}</div>
        </div>
    @endforeach
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th class="px-4 py-2">Date</th><th class="px-4 py-2">Tag</th><th class="px-4 py-2">Batch</th>
                <th class="px-4 py-2">Type</th><th class="px-4 py-2">Description</th><th class="px-4 py-2">Medicine</th>
                <th class="px-4 py-2">Cost</th><th class="px-4 py-2">Performed By</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $r)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2">{{ $r->record_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2"><a href="{{ route('animals.show', $r->animal) }}" class="hover:underline">{{ $r->animal->tag_id }}</a></td>
                    <td class="px-4 py-2"><a href="{{ route('batches.show', $r->animal->batch) }}" class="hover:underline">{{ $r->animal->batch->batch_code }}</a></td>
                    <td class="px-4 py-2 capitalize">
                        <span @class([
                            'px-2 py-0.5 rounded text-xs font-medium',
                            'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400' => $r->record_type === 'death',
                            'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' => $r->record_type === 'vaccination',
                            'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => ! in_array($r->record_type, ['death', 'vaccination']),
                        ])>{{ $r->record_type }}</span>
                    </td>
                    <td class="px-4 py-2">{{ $r->description ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $r->medicine_used ?? '—' }}</td>
                    <td class="px-4 py-2">{{ number_format($r->cost, 2) }}</td>
                    <td class="px-4 py-2">{{ $r->performedBy->full_name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No health records match this filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
