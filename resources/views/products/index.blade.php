@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="flex items-center gap-3 mb-6">
    @include('partials.back-button')
    <h1 class="text-2xl font-semibold">Products</h1>
</div>

<p class="text-sm text-gray-500 dark:text-gray-400 mb-4 max-w-2xl">Every animal currently on feed and available to sell, with its selling price worked out from its latest weight and its species' default price per kg.</p>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th class="px-4 py-2">Tag</th>
                <th class="px-4 py-2">Batch</th>
                <th class="px-4 py-2">Species</th>
                <th class="px-4 py-2">Weight (kg)</th>
                <th class="px-4 py-2">Selling Price</th>
                <th class="px-4 py-2">Health</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="border-t border-gray-200 hover:bg-gray-50 cursor-pointer dark:border-gray-800 dark:hover:bg-gray-800/60" onclick="window.location='{{ route('animals.show', $product->animal) }}'">
                    <td class="px-4 py-2 font-medium">
                        {{ $product->animal->tag_id }}
                        @if ($product->ready_to_sell)
                            <span class="ml-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700 dark:bg-green-500/10 dark:text-green-400">Ready to sell</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">{{ $product->animal->batch->batch_code }}</td>
                    <td class="px-4 py-2">{{ $product->animal->species->name }}</td>
                    <td class="px-4 py-2">{{ number_format($product->weight_kg, 1) }}</td>
                    <td class="px-4 py-2">
                        @if ($product->selling_price !== null)
                            {{ number_format($product->selling_price, 2) }}
                        @else
                            <span class="text-gray-400 dark:text-gray-500">No default price set — <a href="{{ route('species.index') }}" class="underline" onclick="event.stopPropagation()">set one</a></span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        @if ($product->last_health_record)
                            {{ ucfirst($product->last_health_record->record_type) }} on {{ $product->last_health_record->record_date->format('Y-m-d') }}
                        @else
                            <span class="text-gray-400 dark:text-gray-500">No health records yet</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No animals available to sell right now.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
