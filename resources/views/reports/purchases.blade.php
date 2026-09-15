@extends('layouts.app')

@section('title', 'Purchases Report')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-semibold">Purchases Report</h1>
    <div class="flex gap-2">
        <a href="{{ route('reports.purchases.export-pdf', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export PDF</a>
        <a href="{{ route('reports.purchases.export-excel', request()->query()) }}" class="border border-gray-300 text-sm px-3 py-2 rounded-md hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">Export Excel</a>
    </div>
</div>

<form method="GET" class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4 mb-6 flex flex-wrap items-end gap-3">
    <div>
        <label class="block text-sm font-medium mb-1">From</label>
        <input type="date" name="from" value="{{ $from }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">To</label>
        <input type="date" name="to" value="{{ $to }}" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Supplier</label>
        <select name="supplier_id" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">All suppliers</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(request('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Type</label>
        <select name="order_type" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">Any type</option>
            @foreach (['animal', 'feed', 'medicine', 'other'] as $type)
                <option value="{{ $type }}" @selected(request('order_type') === $type)>{{ ucfirst($type) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">Status</label>
        <select name="status" class="border border-gray-300 rounded-md px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800">
            <option value="">Any status</option>
            @foreach (['pending', 'received', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="bg-indigo-600 text-white text-sm px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
</form>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Purchase Orders</div>
        <div class="text-xl font-semibold">{{ $rows->count() }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Total Ordered</div>
        <div class="text-xl font-semibold">{{ number_format($totalOrdered, 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Total Paid</div>
        <div class="text-xl font-semibold">{{ number_format($totalPaid, 2) }}</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 p-4">
        <div class="text-sm text-gray-500 dark:text-gray-400">Outstanding</div>
        <div class="text-xl font-semibold {{ $totalOutstanding > 0 ? 'text-red-600 dark:text-red-400' : '' }}">{{ number_format($totalOutstanding, 2) }}</div>
    </div>
</div>

<div class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600 dark:bg-gray-800 dark:text-gray-300">
            <tr>
                <th class="px-4 py-2">PO #</th><th class="px-4 py-2">Date</th><th class="px-4 py-2">Supplier</th>
                <th class="px-4 py-2">Type</th><th class="px-4 py-2">Total</th><th class="px-4 py-2">Paid</th>
                <th class="px-4 py-2">Balance</th><th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $po)
                <tr class="border-t border-gray-200 dark:border-gray-800">
                    <td class="px-4 py-2"><a href="{{ route('purchase-orders.show', $po) }}" class="hover:underline">{{ $po->po_number }}</a></td>
                    <td class="px-4 py-2">{{ $po->order_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2">{{ $po->supplier->name }}</td>
                    <td class="px-4 py-2 capitalize">{{ $po->order_type }}</td>
                    <td class="px-4 py-2">{{ number_format($po->total_amount, 2) }}</td>
                    <td class="px-4 py-2">{{ number_format($po->amountPaid(), 2) }}</td>
                    <td class="px-4 py-2 {{ $po->balanceDue() > 0 ? 'text-red-600 dark:text-red-400' : '' }}">{{ number_format($po->balanceDue(), 2) }}</td>
                    <td class="px-4 py-2 capitalize">{{ $po->status }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">No purchase orders match this filter.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
