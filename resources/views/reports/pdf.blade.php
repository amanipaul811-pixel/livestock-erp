<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin-bottom: 0; }
        h2 { font-size: 13px; margin: 18px 0 6px; }
        p.subtitle { color: #6b7280; margin-top: 4px; }
        p.note { color: #6b7280; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #e5e7eb; padding: 5px 8px; text-align: left; }
        th { background-color: #f9fafb; }
        td.num, th.num { text-align: right; }
        tr.total td { font-weight: bold; background-color: #f9fafb; }
        .positive { color: #16a34a; }
        .negative { color: #dc2626; }
        .summary-table td { border: none; padding: 3px 12px 3px 0; }
    </style>
</head>
<body>
    <h1>Profit &amp; Loss Report</h1>
    <p class="subtitle">{{ $from }} to {{ $to }}</p>
    <p class="note">Accrual basis: revenue and COGS are recognized only when an animal sells. Animals still on feed carry their cost as WIP inventory, not a loss.</p>

    <table class="summary-table">
        <tr>
            <td>Realized Revenue</td><td class="num">{{ number_format($totals['revenue'], 2) }}</td>
            <td>Net Profit</td>
            <td class="num {{ $totals['net_profit'] >= 0 ? 'positive' : 'negative' }}">{{ number_format($totals['net_profit'], 2) }}</td>
        </tr>
        <tr>
            <td>COGS + Overhead</td>
            <td class="num">{{ number_format($totals['cogs'] + $totals['overhead_expenses'], 2) }}</td>
            <td>Mortality Loss</td>
            <td class="num">{{ number_format($totals['mortality_loss'], 2) }}</td>
        </tr>
    </table>

    <h2>Balance Sheet Snapshot (as of today)</h2>
    <table class="summary-table">
        <tr>
            <td>Livestock WIP Value</td><td class="num">{{ number_format($wipValue, 2) }}</td>
            <td>Feed Inventory Value</td><td class="num">{{ number_format($feedInventoryValue, 2) }}</td>
        </tr>
        <tr>
            <td>Accounts Receivable</td><td class="num">{{ number_format($totalReceivable, 2) }}</td>
            <td>Accounts Payable</td><td class="num">{{ number_format($totalPayable, 2) }}</td>
        </tr>
    </table>

    <h2>Batch Profitability</h2>
    <table>
        <thead>
            <tr>
                <th>Batch</th><th>Status</th>
                <th class="num">Revenue</th><th class="num">COGS</th><th class="num">Mortality</th>
                <th class="num">WIP Value</th><th class="num">Net Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($batchRows as $row)
                <tr>
                    <td>{{ $row['batch']->batch_code }}</td>
                    <td>{{ $row['batch']->status }}</td>
                    <td class="num">{{ number_format($row['realized_revenue'], 2) }}</td>
                    <td class="num">{{ number_format($row['realized_cogs'], 2) }}</td>
                    <td class="num">{{ number_format($row['mortality_loss'], 2) }}</td>
                    <td class="num">{{ number_format($row['wip_value'], 2) }}</td>
                    <td class="num {{ $row['net_profit'] >= 0 ? 'positive' : 'negative' }}">{{ number_format($row['net_profit'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No batches started in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Accounts Receivable Aging</h2>
    <table>
        <thead><tr><th>Customer</th><th>Invoice</th><th class="num">Balance</th><th>Age</th></tr></thead>
        <tbody>
            @forelse ($accountsReceivable as $row)
                <tr><td>{{ $row['party'] }}</td><td>{{ $row['reference'] }}</td><td class="num">{{ number_format($row['balance'], 2) }}</td><td>{{ $row['bucket'] }}</td></tr>
            @empty
                <tr><td colspan="4">Nothing outstanding.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Accounts Payable Aging</h2>
    <table>
        <thead><tr><th>Supplier</th><th>PO</th><th class="num">Balance</th><th>Age</th></tr></thead>
        <tbody>
            @forelse ($accountsPayable as $row)
                <tr><td>{{ $row['party'] }}</td><td>{{ $row['reference'] }}</td><td class="num">{{ number_format($row['balance'], 2) }}</td><td>{{ $row['bucket'] }}</td></tr>
            @empty
                <tr><td colspan="4">Nothing outstanding.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Expenses by Category</h2>
    <table>
        <thead><tr><th>Category</th><th class="num">Total</th></tr></thead>
        <tbody>
            @forelse ($expenseByCategory as $category => $total)
                <tr><td>{{ ucfirst($category) }}</td><td class="num">{{ number_format($total, 2) }}</td></tr>
            @empty
                <tr><td colspan="2">No expenses in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Revenue by Species</h2>
    <table>
        <thead><tr><th>Species</th><th class="num">Total</th></tr></thead>
        <tbody>
            @forelse ($revenueBySpecies as $species => $total)
                <tr><td>{{ $species }}</td><td class="num">{{ number_format($total, 2) }}</td></tr>
            @empty
                <tr><td colspan="2">No sales in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Revenue by Customer</h2>
    <table>
        <thead><tr><th>Customer</th><th class="num">Total</th></tr></thead>
        <tbody>
            @forelse ($revenueByCustomer as $customer => $total)
                <tr><td>{{ $customer }}</td><td class="num">{{ number_format($total, 2) }}</td></tr>
            @empty
                <tr><td colspan="2">No sales in this period.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
