@extends('layouts.app')

@section('title', 'Batches')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Batches</h1>
    @if (auth()->user()->hasPermission('batch.create'))
        <a href="{{ route('batches.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">+ New Batch</a>
    @endif
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th class="px-4 py-2">Code</th>
                <th class="px-4 py-2">Species</th>
                <th class="px-4 py-2">Pen</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Start Date</th>
                <th class="px-4 py-2">Head Count</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($batches as $batch)
                <tr class="border-t border-gray-200 hover:bg-gray-50 cursor-pointer dark:border-gray-800 dark:hover:bg-gray-800/60" onclick="window.location='{{ route('batches.show', $batch) }}'">
                    <td class="px-4 py-2 font-medium">{{ $batch->batch_code }}</td>
                    <td class="px-4 py-2">{{ $batch->species->name }}</td>
                    <td class="px-4 py-2">{{ $batch->pen->name ?? '—' }}</td>
                    <td class="px-4 py-2">
                        <span @class([
                            'px-2 py-0.5 rounded text-xs font-medium',
                            'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400' => $batch->status === 'active',
                            'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400' => $batch->status === 'partially_sold',
                            'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300' => $batch->status === 'closed',
                        ])>{{ $batch->status }}</span>
                    </td>
                    <td class="px-4 py-2">{{ $batch->start_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ $batch->animals()->count() }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No batches yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
