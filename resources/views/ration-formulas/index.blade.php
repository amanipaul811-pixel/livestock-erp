@extends('layouts.app')

@section('title', 'Ration Formulas')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        @include('partials.back-button')
        <h1 class="text-2xl font-semibold">Ration Formulas</h1>
    </div>
    @if (auth()->user()->hasPermission('rationformula.create'))
        <a href="{{ route('ration-formulas.create') }}" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">+ New Ration Formula</a>
    @endif
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Species</th><th class="px-4 py-2">Stage</th></tr>
        </thead>
        <tbody>
            @forelse ($formulas as $formula)
                <tr class="border-t border-gray-200 hover:bg-gray-50 cursor-pointer dark:border-gray-800 dark:hover:bg-gray-800/60" onclick="window.location='{{ route('ration-formulas.show', $formula) }}'">
                    <td class="px-4 py-2 font-medium">{{ $formula->name }}</td>
                    <td class="px-4 py-2">{{ $formula->species->name }}</td>
                    <td class="px-4 py-2">{{ $formula->stage }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No ration formulas yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
