@extends('layouts.app')

@section('title', 'Purchase Orders')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Purchase Orders</h1>
    <a href="{{ route('purchase-orders.create') }}" class="bg-gray-900 text-white text-sm px-4 py-2 rounded hover:bg-gray-700">+ New Purchase Order</a>
</div>

<div class="bg-white rounded shadow overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-100 text-left text-gray-600">
            <tr>
                <th class="px-4 py-2">PO Number</th>
                <th class="px-4 py-2">Supplier</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Date</th>
                <th class="px-4 py-2">Total</th>
                <th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr class="border-t hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('purchase-orders.show', $order) }}'">
                    <td class="px-4 py-2 font-medium">{{ $order->po_number }}</td>
                    <td class="px-4 py-2">{{ $order->supplier->name }}</td>
                    <td class="px-4 py-2">{{ $order->order_type }}</td>
                    <td class="px-4 py-2">{{ $order->order_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-4 py-2">
                        <span @class([
                            'px-2 py-0.5 rounded text-xs font-medium',
                            'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                            'bg-green-100 text-green-700' => $order->status === 'received',
                            'bg-gray-200 text-gray-700' => $order->status === 'cancelled',
                        ])>{{ $order->status }}</span>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-6 text-center text-gray-500">No purchase orders yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
