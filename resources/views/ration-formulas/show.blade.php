@extends('layouts.app')

@section('title', $formula->name)

@section('content')
<div class="flex items-center justify-between mb-1">
    <h1 class="text-2xl font-semibold">{{ $formula->name }}</h1>
    @if (auth()->user()->hasPermission('rationformula.create'))
    <div class="flex gap-2">
        <a href="{{ route('ration-formulas.edit', $formula) }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Edit</a>
        <form method="POST" action="{{ route('ration-formulas.destroy', $formula) }}" onsubmit="return confirm('Delete this ration formula?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="border border-red-300 text-red-600 text-sm px-4 py-2 rounded-md hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-500/10">Delete</button>
        </form>
    </div>
    @endif
</div>
<p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ $formula->species->name }} &middot; Stage: {{ $formula->stage }}</p>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 mb-6 max-w-sm">
    <div class="text-xs text-gray-500 dark:text-gray-400">Daily Cost per Head</div>
    <div class="text-xl font-semibold">{{ number_format($dailyCostPerHead, 2) }}</div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden max-w-2xl">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr><th class="px-4 py-2">Feed Item</th><th class="px-4 py-2">Kg / Head / Day</th><th class="px-4 py-2">Cost / Head / Day</th></tr>
        </thead>
        <tbody>
            @foreach ($formula->items as $item)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2 font-medium">{{ $item->feedItem->name }}</td>
                    <td class="px-4 py-2">{{ $item->quantity_kg_per_head }}</td>
                    <td class="px-4 py-2">{{ number_format($item->quantity_kg_per_head * $item->feedItem->cost_per_unit, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
