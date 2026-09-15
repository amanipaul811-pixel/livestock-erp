@extends('layouts.app')

@section('title', 'Edit '.$batch->batch_code)

@section('content')
<div class="flex items-start gap-3 mb-6">
    @include('partials.back-button', ['fallback' => route('batches.show', $batch)])
    <div>
        <h1 class="text-2xl font-semibold mb-1">Edit {{ $batch->batch_code }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">Species and start date are fixed once a batch is created.</p>
    </div>
</div>

<form method="POST" action="{{ route('batches.update', $batch) }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 max-w-lg space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium mb-1">Pen</label>
        <select name="pen_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">Unassigned</option>
            @foreach ($pens as $pen)
                <option value="{{ $pen->id }}" @selected(old('pen_id', $batch->pen_id) == $pen->id)>{{ $pen->name }} ({{ $pen->stage }})</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            @foreach (['active', 'partially_sold', 'closed'] as $status)
                <option value="{{ $status }}" @selected(old('status', $batch->status) === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Expected End Date</label>
            <input type="date" name="expected_end_date" value="{{ old('expected_end_date', optional($batch->expected_end_date)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Actual End Date</label>
            <input type="date" name="actual_end_date" value="{{ old('actual_end_date', optional($batch->actual_end_date)->format('Y-m-d')) }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Notes</label>
        <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">{{ old('notes', $batch->notes) }}</textarea>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Save</button>
        <a href="{{ route('batches.show', $batch) }}" class="border border-gray-300 text-sm px-4 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Cancel</a>
    </div>
</form>
@endsection
