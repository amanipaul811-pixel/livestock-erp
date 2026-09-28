<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 22px; margin: 0; }
        table.header { width: 100%; margin-bottom: 24px; }
        table.header td { vertical-align: top; }
        table.header td.meta { text-align: right; }
        .meta-table { margin-left: auto; }
        .meta-table td { padding: 1px 0 1px 12px; }
        .meta-table td.label { color: #6b7280; text-align: right; }
        h2 { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280; margin: 0 0 4px; }
        .bill-to { margin-bottom: 24px; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .items th, .items td { border: 1px solid #e5e7eb; padding: 6px 10px; text-align: left; }
        .items th { background-color: #f9fafb; }
        .items td.num, .items th.num { text-align: right; }
        table.totals { width: 260px; margin-left: auto; border-collapse: collapse; }
        .totals td { padding: 4px 0; }
        .totals td.label { color: #6b7280; }
        .totals td.num { text-align: right; }
        .totals tr.balance td { font-weight: bold; border-top: 1px solid #e5e7eb; padding-top: 8px; }
        .status-completed { color: #16a34a; }
        .status-pending { color: #b8862b; }
        table.payments { width: 100%; border-collapse: collapse; margin-top: 24px; }
        .payments th, .payments td { border: 1px solid #e5e7eb; padding: 5px 8px; text-align: left; }
        .payments th { background-color: #f9fafb; }
        .payments td.num, .payments th.num { text-align: right; }
        .footer-note { margin-top: 32px; color: #6b7280; font-size: 10px; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td><h1>Livestock ERP</h1></td>
            <td class="meta">
                <table class="meta-table">
                    <tr><td class="label">Invoice</td><td>{{ $order->so_number }}</td></tr>
                    <tr><td class="label">Sale Date</td><td>{{ $order->sale_date->format('Y-m-d') }}</td></tr>
                    <tr><td class="label">Status</td><td class="status-{{ $order->status }}">{{ ucfirst($order->status) }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="bill-to">
        <h2>Bill To</h2>
        {{ $order->customer->name }}<br>
        @if ($order->customer->phone)
            {{ $order->customer->phone }}<br>
        @endif
        @if ($order->customer->email)
            {{ $order->customer->email }}<br>
        @endif
        @if ($order->customer->address)
            {{ $order->customer->address }}
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>Animal</th>
                <th>Species</th>
                <th class="num">Sale Weight</th>
                <th class="num">Price/kg</th>
                <th class="num">Line Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->animal->tag_id }}</td>
                    <td>{{ $item->animal->species->name }}</td>
                    <td class="num">{{ number_format($item->sale_weight_kg, 2) }} kg</td>
                    <td class="num">{{ number_format($item->price_per_kg, 2) }}</td>
                    <td class="num">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td class="label">Total Amount</td><td class="num">{{ number_format($order->total_amount, 2) }}</td></tr>
        <tr><td class="label">Amount Paid</td><td class="num">{{ number_format($amountPaid, 2) }}</td></tr>
        <tr class="balance"><td>Balance Due</td><td class="num">{{ number_format($balanceDue, 2) }}</td></tr>
    </table>

    @if ($payments->isNotEmpty())
        <table class="payments">
            <thead>
                <tr><th>Date</th><th>Method</th><th class="num">Amount</th></tr>
            </thead>
            <tbody>
                @foreach ($payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $payment->method)) }}</td>
                        <td class="num">{{ number_format($payment->amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="footer-note">Generated from Livestock ERP on {{ now()->format('Y-m-d') }}.</p>
</body>
</html>
