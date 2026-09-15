@extends('layouts.app')

@section('title', 'Ration Formulas')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Ration Formulas</h1>
    @if (auth()->user()->hasPermission('rationformula.create'))
        <a href="{{ route('ration-formulas.create') }}" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">+ New Ration Formula</a>
    @endif
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-left text-gray-600">
            <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Species</th><th class="px-4 py-2">Stage</th></tr>
        </thead>
        <tbody>
            @forelse ($formulas as $formula)
                <tr class="border-t hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('ration-formulas.show', $formula) }}'">
                    <td class="px-4 py-2 font-medium">{{ $formula->name }}</td>
                    <td class="px-4 py-2">{{ $formula->species->name }}</td>
                    <td class="px-4 py-2">{{ $formula->stage }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">No ration formulas yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
