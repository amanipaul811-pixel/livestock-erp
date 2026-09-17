@extends('layouts.app')

@section('title', 'Species Pricing')

@section('content')
<div class="flex items-center gap-3 mb-6">
    @include('partials.back-button')
    <h1 class="text-2xl font-semibold">Species Pricing</h1>
</div>

<p class="text-sm text-gray-500 dark:text-gray-400 mb-4 max-w-xl">Set a default price per kg for each species. Recording a sale will pre-fill that price for animals of this species, and you can still change it for any individual sale.</p>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden max-w-xl">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr><th class="px-4 py-2">Species</th><th class="px-4 py-2">Default Price/kg</th><th class="px-4 py-2"></th></tr>
        </thead>
        <tbody>
            @foreach ($speciesList as $species)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2 font-medium">{{ $species->name }}</td>
                    <td class="px-4 py-2">
                        <form method="POST" action="{{ route('species.update-pricing', $species) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PUT')
                            <input type="number" step="0.01" min="0" name="default_price_per_kg" value="{{ old('default_price_per_kg', $species->default_price_per_kg) }}" placeholder="Not set" class="w-28 border border-gray-300 rounded-md px-2 py-1 text-sm dark:border-gray-700 dark:bg-gray-800">
                            <button type="submit" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs">Save</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
