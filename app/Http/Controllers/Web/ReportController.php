<?php

namespace App\Http\Controllers\Web;

use App\Exports\ProfitAndLossExport;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Expense;
use App\Models\FeedItem;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.index', $this->reportData($request));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->reportData($request);

        return Pdf::loadView('reports.pdf', $data)
            ->download("profit-and-loss-{$data['from']}-to-{$data['to']}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $data = $this->reportData($request);

        return (new ProfitAndLossExport($data))
            ->download("profit-and-loss-{$data['from']}-to-{$data['to']}.xlsx");
    }

    private function reportData(Request $request): array
    {
        $from = $request->query('from') ?: now()->subMonths(12)->startOfMonth()->toDateString();
        $to = $request->query('to') ?: now()->toDateString();

        // Accrual P&L for the period: realized revenue/COGS for animals that
        // actually sold, a mortality loss for animals that died, and costs
        // for animals still on feed held out as WIP inventory rather than
        // counted as a loss (see Batch::accrualBreakdown()).
        $batches = Batch::whereBetween('start_date', [$from, $to])->orderBy('start_date')->get();
        $batchRows = $batches->map(fn (Batch $b) => array_merge(['batch' => $b], $b->accrualBreakdown()));

        $overheadExpenses = (float) Expense::whereNull('batch_id')
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $totals = [
            'revenue' => (float) $batchRows->sum('realized_revenue'),
            'cogs' => (float) $batchRows->sum('realized_cogs'),
            'mortality_loss' => (float) $batchRows->sum('mortality_loss'),
            'overhead_expenses' => $overheadExpenses,
            'net_profit' => (float) $batchRows->sum('net_profit') - $overheadExpenses,
        ];

        // Balance-sheet-style snapshot: point-in-time, not scoped to the
        // period above -- what's currently tied up in animals still on feed
        // and feed sitting in the store, valued at cost.
        $wipValue = (float) Batch::whereIn('status', ['active', 'partially_sold'])
            ->get()
            ->sum(fn (Batch $b) => $b->accrualBreakdown()['wip_value']);

        $feedInventoryValue = (float) FeedItem::all()
            ->sum(fn (FeedItem $item) => $item->currentStock() * $item->cost_per_unit);

        [$accountsPayable, $totalPayable] = $this->aging(
            PurchaseOrder::with('supplier')->where('status', '!=', 'cancelled')->get(),
            fn (PurchaseOrder $po) => $po->po_number,
            fn (PurchaseOrder $po) => $po->supplier->name,
            fn (PurchaseOrder $po) => $po->order_date,
        );

        [$accountsReceivable, $totalReceivable] = $this->aging(
            SalesOrder::with('customer')->where('status', '!=', 'cancelled')->get(),
            fn (SalesOrder $so) => $so->so_number,
            fn (SalesOrder $so) => $so->customer->name,
            fn (SalesOrder $so) => $so->sale_date,
        );

        $expenseByCategory = Expense::whereBetween('expense_date', [$from, $to])
            ->selectRaw('category, sum(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->pluck('total', 'category');

        $revenueBySpecies = SalesOrderItem::join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
            ->join('animals', 'animals.id', '=', 'sales_order_items.animal_id')
            ->join('species', 'species.id', '=', 'animals.species_id')
            ->whereBetween('sales_orders.sale_date', [$from, $to])
            ->selectRaw('species.name as species, sum(sales_order_items.line_total) as total')
            ->groupBy('species.name')
            ->orderByDesc('total')
            ->pluck('total', 'species');

        $revenueByCustomer = SalesOrder::join('customers', 'customers.id', '=', 'sales_orders.customer_id')
            ->whereBetween('sales_orders.sale_date', [$from, $to])
            ->selectRaw('customers.name as customer, sum(sales_orders.total_amount) as total')
            ->groupBy('customers.name')
            ->orderByDesc('total')
            ->pluck('total', 'customer');

        return compact(
            'batchRows', 'totals', 'wipValue', 'feedInventoryValue',
            'accountsPayable', 'totalPayable', 'accountsReceivable', 'totalReceivable',
            'expenseByCategory', 'revenueBySpecies', 'revenueByCustomer', 'from', 'to'
        );
    }

    /**
     * Build an aging list (current / 31-60 / 61-90 / 90+ days outstanding)
     * for any collection of orders that expose a balanceDue(), shared by
     * both accounts payable (purchase orders) and receivable (sales orders).
     */
    private function aging($orders, callable $reference, callable $party, callable $date): array
    {
        $rows = $orders
            ->map(function ($order) use ($reference, $party, $date) {
                $days = (int) $date($order)->diffInDays(now());

                return [
                    'reference' => $reference($order),
                    'party' => $party($order),
                    'date' => $date($order),
                    'balance' => $order->balanceDue(),
                    'days' => $days,
                    'bucket' => match (true) {
                        $days <= 30 => 'Current',
                        $days <= 60 => '31-60 days',
                        $days <= 90 => '61-90 days',
                        default => '90+ days',
                    },
                ];
            })
            ->filter(fn (array $row) => $row['balance'] > 0.01)
            ->sortByDesc('days')
            ->values();

        return [$rows, (float) $rows->sum('balance')];
    }
}
