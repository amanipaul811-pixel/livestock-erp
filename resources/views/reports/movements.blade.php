@extends('layouts.app')

@section('title', 'Animal Movement Report')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="flex items-center gap-3">
        @include('partials.back-button')
        <h1 class="text-2xl font-semibold">Animal Movement Report</h1>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('reports.movements.export-pdf', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export PDF</a>
        <a href="{{ route('reports.movements.export-excel', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export Excel</a>
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
        <label class="block text-sm font-medium mb-1">Pen</label>
        <select name="pen_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">All pens</option>
            @foreach ($pens as $pen)
                <option value="{{ $pen->id }}" @selected(request('pen_id') == $pen->id)>{{ $pen->name }}</option>
            @endforeach
        </select>
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
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
</form>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 mb-6 max-w-xs">
    <div class="text-sm text-gray-500 dark:text-gray-400">Total Movements</div>
    <div class="text-xl font-semibold">{{ $rows->count() }}</div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th class="px-4 py-2">Date</th><th class="px-4 py-2">Tag</th><th class="px-4 py-2">Species</th>
                <th class="px-4 py-2">Batch</th><th class="px-4 py-2">From Pen</th><th class="px-4 py-2">To Pen</th>
                <th class="px-4 py-2">Reason</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $m)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2">{{ $m->move_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2"><a href="{{ route('animals.show', $m->animal) }}" class="hover:underline">{{ $m->animal->tag_id }}</a></td>
                    <td class="px-4 py-2">{{ $m->animal->species->name }}</td>
                    <td class="px-4 py-2"><a href="{{ route('batches.show', $m->animal->batch) }}" class="hover:underline">{{ $m->animal->batch->batch_code }}</a></td>
                    <td class="px-4 py-2">{{ $m->fromPen->name ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $m->toPen->name ?? '—' }}</td>
                    <td class="px-4 py-2">{{ $m->reason ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No movements match this filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
