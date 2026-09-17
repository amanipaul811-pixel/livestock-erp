@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        @include('partials.back-button')
        <h1 class="text-2xl font-semibold">Calendar</h1>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('calendar', ['month' => $prevMonth]) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">&larr; Prev</a>
        <span class="text-sm font-medium w-32 text-center">{{ $month->format('F Y') }}</span>
        <a href="{{ route('calendar', ['month' => $nextMonth]) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Next &rarr;</a>
        <a href="{{ route('calendar') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline ml-2">Today</a>
    </div>
</div>

<div class="flex flex-wrap gap-3 mb-4 text-xs">
    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-green-500"></span> Sale</span>
    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-indigo-500"></span> Purchase</span>
    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-purple-500"></span> Pen movement</span>
    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-red-500"></span> Health record</span>
    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-teal-500"></span> Weigh-ins</span>
    <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-yellow-500"></span> Batch expected ready</span>
</div>

@php
    $colors = [
        'sale' => 'bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400',
        'purchase' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400',
        'movement' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400',
        'health' => 'bg-red-100 text-red-700 dark:bg-red-500/10 dark:text-red-400',
        'weighin' => 'bg-teal-100 text-teal-700 dark:bg-teal-500/10 dark:text-teal-400',
        'batch_due' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/10 dark:text-yellow-400',
    ];
@endphp

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <div class="grid grid-cols-7 bg-gray-50 dark:bg-gray-800 text-xs font-semibold text-gray-600 dark:text-gray-300">
        @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $label)
            <div class="px-2 py-2 text-center border-r border-gray-200 dark:border-gray-800 last:border-r-0">{{ $label }}</div>
        @endforeach
    </div>
    @foreach ($weeks as $week)
        <div class="grid grid-cols-7 border-t border-gray-200 dark:border-gray-800">
            @foreach ($week as $day)
                @php
                    $dayEvents = $events[$day->toDateString()] ?? [];
                    $inMonth = $day->month === $month->month;
                    $isToday = $day->isToday();
                @endphp
                <div class="min-h-[92px] border-r border-gray-200 dark:border-gray-800 last:border-r-0 p-1.5 align-top {{ $inMonth ? '' : 'bg-gray-50/50 dark:bg-gray-800/30' }}">
                    <div class="text-xs mb-1 {{ $isToday ? 'inline-flex h-5 w-5 items-center justify-center rounded-full bg-indigo-600 text-white font-semibold' : ($inMonth ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600') }}">{{ $day->day }}</div>
                    <div class="space-y-0.5">
                        @foreach (array_slice($dayEvents, 0, 3) as $event)
                            @if ($event['url'])
                                <a href="{{ $event['url'] }}" class="block truncate text-[11px] px-1 py-0.5 rounded {{ $colors[$event['type']] }}" title="{{ $event['label'] }}">{{ $event['label'] }}</a>
                            @else
                                <div class="block truncate text-[11px] px-1 py-0.5 rounded {{ $colors[$event['type']] }}" title="{{ $event['label'] }}">{{ $event['label'] }}</div>
                            @endif
                        @endforeach
                        @if (count($dayEvents) > 3)
                            <div class="text-[11px] text-gray-500 dark:text-gray-400 px-1">+{{ count($dayEvents) - 3 }} more</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</div>
@endsection
