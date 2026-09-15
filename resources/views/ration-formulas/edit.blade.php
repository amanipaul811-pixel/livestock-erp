@extends('layouts.app')

@section('title', 'Edit '.$formula->name)

@section('content')
<h1 class="text-2xl font-semibold mb-6">Edit {{ $formula->name }}</h1>

<form method="POST" action="{{ route('ration-formulas.update', $formula) }}"
      x-data="{ rows: {{ $formula->items->map(fn ($i) => ['feed_item_id' => (string) $i->feed_item_id, 'quantity_kg_per_head' => $i->quantity_kg_per_head])->toJson() }} }"
      class="bg-white rounded shadow p-6 space-y-6 max-w-2xl">
    @csrf
    @method('PUT')

    <div>
        <label class="block text-sm font-medium mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $formula->name) }}" required class="w-full border rounded px-3 py-2 text-sm">
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium mb-1">Species</label>
            <select name="species_id" required class="w-full border rounded px-3 py-2 text-sm">
                @foreach ($speciesList as $species)
                    <option value="{{ $species->id }}" @selected(old('species_id', $formula->species_id) == $species->id)>{{ $species->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Stage</label>
            <select name="stage" required class="w-full border rounded px-3 py-2 text-sm">
                @foreach (['starter', 'growing', 'finishing'] as $stage)
                    <option value="{{ $stage }}" @selected(old('stage', $formula->stage) === $stage)>{{ ucfirst($stage) }}</option>
                @endforeach
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

    <div class="flex gap-2">
        <button type="submit" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">Save</button>
        <a href="{{ route('ration-formulas.show', $formula) }}" class="border text-sm px-4 py-2 rounded hover:bg-gray-100">Cancel</a>
    </div>
</form>
@endsection
