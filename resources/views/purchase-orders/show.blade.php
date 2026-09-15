@extends('layouts.app')

@section('title', $order->po_number)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-semibold">{{ $order->po_number }}</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Supplier: {{ $order->supplier->name }} &middot; Type: {{ $order->order_type }} &middot;
            Date: {{ $order->order_date->format('Y-m-d') }} &middot; Status: {{ $order->status }}
        </p>
    </div>
    @if ($order->status === 'pending' && auth()->user()->hasPermission('purchaseorder.update'))
        <div class="flex gap-2">
            <form method="POST" action="{{ route('purchase-orders.update-status', $order) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="received">
                <button type="submit" class="bg-green-600 text-white text-sm px-3 py-1.5 rounded hover:bg-green-700">Mark Received</button>
            </form>
            <form method="POST" action="{{ route('purchase-orders.update-status', $order) }}" onsubmit="return confirm('Cancel this purchase order?');">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" class="border border-red-300 text-red-600 text-sm px-3 py-1.5 rounded-md hover:bg-red-50 dark:border-red-800 dark:text-red-400 dark:hover:bg-red-500/10">Cancel</button>
            </form>
        </div>
    @endif
</div>

<div class="grid grid-cols-3 gap-4 mb-8">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Total Amount</div>
        <div class="text-xl font-semibold">{{ number_format($order->total_amount, 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Amount Paid</div>
        <div class="text-xl font-semibold">{{ number_format($amountPaid, 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-xs text-gray-500 dark:text-gray-400">Balance Due</div>
        <div class="text-xl font-semibold {{ $balanceDue > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($balanceDue, 2) }}</div>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 max-w-xl">
    <h2 class="font-semibold mb-3">Payments</h2>
    <table class="w-full text-sm mb-4">
        <thead class="text-left text-gray-500 dark:text-gray-400">
            <tr><th class="py-1">Date</th><th class="py-1">Method</th><th class="py-1">Amount</th></tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="py-1.5">{{ $payment->payment_date->format('Y-m-d') }}</td>
                    <td class="py-1.5">{{ $payment->method }}</td>
                    <td class="py-1.5">{{ number_format($payment->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="py-4 text-center text-gray-500 dark:text-gray-400">No payments recorded yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if ($balanceDue > 0)
        <form method="POST" action="{{ route('purchase-order-payments.store', $order) }}" class="border-t border-gray-200 dark:border-gray-800 pt-4 grid grid-cols-2 gap-2">
            @csrf
            <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <input type="number" step="0.01" name="amount" placeholder="Amount (max {{ number_format($balanceDue, 2) }})" max="{{ $balanceDue }}" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800">
            <select name="method" required class="border border-gray-300 rounded-md px-2 py-1.5 text-sm dark:border-gray-700 dark:bg-gray-800 col-span-2">
                <option value="cash">Cash</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="mobile_money">Mobile Money</option>
                <option value="cheque">Cheque</option>
            </select>
            <button type="submit" class="col-span-2 bg-indigo-600 text-white text-sm px-3 py-1.5 rounded-md hover:bg-indigo-700">Record Payment</button>
        </form>
    @else
        <p class="text-sm text-green-600 border-t pt-4">Paid in full.</p>
    @endif
</div>
@endsection
