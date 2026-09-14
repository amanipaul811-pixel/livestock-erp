@extends('layouts.app')

@section('title', 'New Ration Formula')

@section('content')
<h1 class="text-2xl font-semibold mb-6">New Ration Formula</h1>

@if ($feedItems->isEmpty())
    <div class="bg-white rounded shadow p-6 text-sm text-gray-500">
        No feed items yet — <a href="{{ route('feed-items.index') }}" class="underline">add one</a> first.
    </div>
@else
<form method="POST" action="{{ route('ration-formulas.store') }}"
      x-data="{ rows: [{ feed_item_id: '', quantity_kg_per_head: '' }] }"
      class="bg-white rounded shadow p-6 space-y-6 max-w-2xl">
    @csrf

    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" required placeholder="e.g. Cattle Finishing Ration" class="w-full border rounded px-3 py-2 text-sm">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Species</label>
            <select name="species_id" required class="w-full border rounded px-3 py-2 text-sm">
                <option value="">Select species</option>
                @foreach ($speciesList as $species)
                    <option value="{{ $species->id }}">{{ $species->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Stage</label>
            <select name="stage" required class="w-full border rounded px-3 py-2 text-sm">
                <option value="starter">Starter</option>
                <option value="growing">Growing</option>
                <option value="finishing">Finishing</option>
            </select>
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <h2 class="font-semibold text-sm">Feed Items (daily amount per head)</h2>
            <button type="button" @click="rows.push({ feed_item_id: '', quantity_kg_per_head: '' })"
                    class="text-sm text-blue-600 hover:underline">+ Add feed item</button>
        </div>

        <template x-for="(row, index) in rows" :key="index">
            <div class="grid grid-cols-12 gap-2 mb-2 items-center">
                <select :name="`items[${index}][feed_item_id]`" x-model="row.feed_item_id" required class="col-span-8 border rounded px-2 py-1.5 text-sm">
                    <option value="">Select feed item</option>
                    @foreach ($feedItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                    @endforeach
                </select>
                <input :name="`items[${index}][quantity_kg_per_head]`" x-model="row.quantity_kg_per_head" type="number" step="0.01" placeholder="kg/head/day" required class="col-span-3 border rounded px-2 py-1.5 text-sm">
                <button type="button" @click="rows.splice(index, 1)" x-show="rows.length > 1" class="col-span-1 text-red-500 hover:text-red-700 text-sm">✕</button>
            </div>
        </template>
    </div>

    <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">Create Ration Formula</button>
</form>
@endif
@endsection
