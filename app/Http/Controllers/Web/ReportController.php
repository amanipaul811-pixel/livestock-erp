<?php

namespace App\Http\Controllers\Web;

use App\Exports\ProfitAndLossExport;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Expense;
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

        $batches = Batch::whereBetween('start_date', [$from, $to])->orderBy('start_date')->get();

        $batchRows = $batches->map(fn (Batch $b) => [
            'batch' => $b,
            'revenue' => $b->totalSalesRevenue(),
            'purchase_cost' => $b->totalPurchaseCost(),
            'feed_cost' => $b->totalFeedCost(),
            'health_cost' => $b->totalHealthCost(),
            'other_expenses' => $b->totalOtherExpenses(),
            'net_profit' => $b->netProfit(),
        ]);

        $overheadExpenses = (float) Expense::whereNull('batch_id')
            ->whereBetween('expense_date', [$from, $to])
            ->sum('amount');

        $totals = [
            'revenue' => (float) $batchRows->sum('revenue'),
            'purchase_cost' => (float) $batchRows->sum('purchase_cost'),
            'feed_cost' => (float) $batchRows->sum('feed_cost'),
            'health_cost' => (float) $batchRows->sum('health_cost'),
            'other_expenses' => (float) $batchRows->sum('other_expenses'),
            'overhead_expenses' => $overheadExpenses,
            'net_profit' => (float) $batchRows->sum('net_profit') - $overheadExpenses,
        ];

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

        return compact('batchRows', 'totals', 'expenseByCategory', 'revenueBySpecies', 'revenueByCustomer', 'from', 'to');
    }
}
