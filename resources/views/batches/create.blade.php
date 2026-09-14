@extends('layouts.app')

@section('title', 'New Batch')

@section('content')
<h1 class="text-2xl font-semibold mb-6">New Batch (Buy)</h1>

<form method="POST" action="{{ route('batches.store') }}" class="bg-white rounded shadow p-6 max-w-lg space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Batch Code</label>
        <input type="text" name="batch_code" value="{{ old('batch_code') }}" required placeholder="e.g. B-2026-001"
               class="w-full border rounded px-3 py-2 text-sm">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Species</label>
        <select name="species_id" required class="w-full border rounded px-3 py-2 text-sm">
            <option value="">Select species</option>
            @foreach ($speciesList as $species)
                <option value="{{ $species->id }}" @selected(old('species_id') == $species->id)>{{ $species->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Pen</label>
        <select name="pen_id" class="w-full border rounded px-3 py-2 text-sm">
            <option value="">Unassigned</option>
            @foreach ($pens as $pen)
                <option value="{{ $pen->id }}" @selected(old('pen_id') == $pen->id)>{{ $pen->name }} ({{ $pen->stage }})</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Start Date</label>
            <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Expected End Date</label>
            <input type="date" name="expected_end_date" value="{{ old('expected_end_date') }}"
                   class="w-full border rounded px-3 py-2 text-sm">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Notes</label>
        <textarea name="notes" rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('notes') }}</textarea>
    </div>
    <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">Create Batch</button>
</form>
@endsection
