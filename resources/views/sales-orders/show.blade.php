@extends('layouts.app')

@section('title', $order->so_number)

@section('content')
<h1 class="text-2xl font-semibold mb-1">{{ $order->so_number }}</h1>
<p class="text-sm text-gray-500 mb-6">
    Customer: {{ $order->customer->name }} &middot; Sale Date: {{ $order->sale_date->format('Y-m-d') }} &middot; Status: {{ $order->status }}
</p>

<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs text-gray-500">Total Amount</div>
        <div class="text-xl font-semibold">{{ number_format($order->total_amount, 2) }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs text-gray-500">Amount Paid</div>
        <div class="text-xl font-semibold">{{ number_format($amountPaid, 2) }}</div>
    </div>
    <div class="bg-white rounded shadow p-4">
        <div class="text-xs text-gray-500">Balance Due</div>
        <div class="text-xl font-semibold {{ $balanceDue > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($balanceDue, 2) }}</div>
    </div>
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-left text-gray-600">
            <tr><th class="px-4 py-2">Animal</th><th class="px-4 py-2">Sale Weight</th><th class="px-4 py-2">Price/kg</th><th class="px-4 py-2">Line Total</th></tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr class="border-t">
                    <td class="px-4 py-2"><a href="{{ route('animals.show', $item->animal) }}" class="underline">{{ $item->animal->tag_id }}</a></td>
                    <td class="px-4 py-2">{{ $item->sale_weight_kg }} kg</td>
                    <td class="px-4 py-2">{{ number_format($item->price_per_kg, 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
