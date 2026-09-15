@extends('layouts.app')

@section('title', 'Intake Animal')

@section('content')
<h1 class="text-2xl font-semibold mb-1">Intake Animal</h1>
<p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Into batch {{ $batch->batch_code }} ({{ $batch->species->name }})</p>

<form method="POST" action="{{ route('animals.store', $batch) }}" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-6 max-w-lg space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium mb-1">Tag ID</label>
        <input type="text" name="tag_id" value="{{ old('tag_id') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Breed</label>
            <input type="text" name="breed" value="{{ old('breed') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Sex</label>
            <select name="sex" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Entry Date</label>
            <input type="date" name="entry_date" value="{{ old('entry_date', now()->format('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Estimated Age (months)</label>
            <input type="number" name="estimated_age_months" value="{{ old('estimated_age_months') }}" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Entry Weight (kg)</label>
            <input type="number" step="0.01" name="entry_weight_kg" value="{{ old('entry_weight_kg') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Purchase Price</label>
            <input type="number" step="0.01" name="purchase_price" value="{{ old('purchase_price') }}" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Pen</label>
            <select name="current_pen_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="">Unassigned</option>
                @foreach ($pens as $pen)
                    <option value="{{ $pen->id }}" @selected(old('current_pen_id') == $pen->id)>{{ $pen->name }} ({{ $pen->stage }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Supplier</label>
            <select name="supplier_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
                <option value="">None</option>
                @foreach ($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Add Animal</button>
</form>
@endsection
