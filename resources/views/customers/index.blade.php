@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<h1 class="text-2xl font-semibold mb-6">Customers</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left text-gray-600">
                <tr><th class="px-4 py-2">Name</th><th class="px-4 py-2">Type</th><th class="px-4 py-2">Phone</th></tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr class="border-t">
                        <td class="px-4 py-2 font-medium">{{ $customer->name }}</td>
                        <td class="px-4 py-2">{{ $customer->customer_type }}</td>
                        <td class="px-4 py-2">{{ $customer->phone ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">No customers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form method="POST" action="{{ route('customers.store') }}" class="bg-white rounded shadow p-4 space-y-3 h-fit">
        @csrf
        <h2 class="font-semibold">Add Customer</h2>
        <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" required class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Type</label>
            <select name="customer_type" required class="w-full border rounded px-3 py-2 text-sm">
                <option value="individual">Individual</option>
                <option value="butcher">Butcher</option>
                <option value="trader">Trader</option>
                <option value="exporter">Exporter</option>
                <option value="other">Other</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Phone</label>
            <input type="text" name="phone" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <button type="submit" class="w-full bg-gray-900 text-white text-sm px-3 py-2 rounded hover:bg-gray-700">Add</button>
    </form>
</div>
@endsection
