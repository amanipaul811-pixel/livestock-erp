<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin-bottom: 0; }
        h2 { font-size: 13px; margin: 18px 0 6px; }
        p.subtitle { color: #6b7280; margin-top: 4px; }
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

    <table class="summary-table">
        <tr>
            <td>Revenue</td><td class="num">{{ number_format($totals['revenue'], 2) }}</td>
            <td>Net Profit</td>
            <td class="num {{ $totals['net_profit'] >= 0 ? 'positive' : 'negative' }}">{{ number_format($totals['net_profit'], 2) }}</td>
        </tr>
        <tr>
            <td>Total Costs</td>
            <td class="num">{{ number_format($totals['purchase_cost'] + $totals['feed_cost'] + $totals['health_cost'] + $totals['other_expenses'] + $totals['overhead_expenses'], 2) }}</td>
            <td>Overhead (unassigned)</td>
            <td class="num">{{ number_format($totals['overhead_expenses'], 2) }}</td>
        </tr>
    </table>

    <h2>Batch Profitability</h2>
    <table>
        <thead>
            <tr>
                <th>Batch</th><th>Status</th>
                <th class="num">Revenue</th><th class="num">Purchase</th><th class="num">Feed</th>
                <th class="num">Health</th><th class="num">Other</th><th class="num">Net Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($batchRows as $row)
                <tr>
                    <td>{{ $row['batch']->batch_code }}</td>
                    <td>{{ $row['batch']->status }}</td>
                    <td class="num">{{ number_format($row['revenue'], 2) }}</td>
                    <td class="num">{{ number_format($row['purchase_cost'], 2) }}</td>
                    <td class="num">{{ number_format($row['feed_cost'], 2) }}</td>
                    <td class="num">{{ number_format($row['health_cost'], 2) }}</td>
                    <td class="num">{{ number_format($row['other_expenses'], 2) }}</td>
                    <td class="num {{ $row['net_profit'] >= 0 ? 'positive' : 'negative' }}">{{ number_format($row['net_profit'], 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No batches started in this period.</td></tr>
            @endforelse
            <tr class="total">
                <td colspan="2">TOTAL</td>
                <td class="num">{{ number_format($totals['revenue'], 2) }}</td>
                <td class="num">{{ number_format($totals['purchase_cost'], 2) }}</td>
                <td class="num">{{ number_format($totals['feed_cost'], 2) }}</td>
                <td class="num">{{ number_format($totals['health_cost'], 2) }}</td>
                <td class="num">{{ number_format($totals['other_expenses'], 2) }}</td>
                <td class="num">{{ number_format($totals['net_profit'], 2) }}</td>
            </tr>
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
