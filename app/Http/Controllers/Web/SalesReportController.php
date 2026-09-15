<?php

namespace App\Http\Controllers\Web;

use App\Exports\TableExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\SalesOrderItem;
use App\Models\Species;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.sales', $this->data($request));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->data($request);

        return Pdf::loadView('reports.table-pdf', [
            'title' => 'Sales Report',
            'subtitle' => "{$data['from']} to {$data['to']}",
            'headings' => ['SO #', 'Date', 'Customer', 'Tag', 'Species', 'Weight (kg)', 'Price/kg', 'Line Total', 'Status'],
            'rows' => $data['rows']->map(fn ($r) => [
                $r->so_number, $r->sale_date, $r->customer, $r->tag_id, $r->species,
                number_format($r->sale_weight_kg, 2), number_format($r->price_per_kg, 2),
                number_format($r->line_total, 2), $r->status,
            ]),
            'summary' => [
                'Animals Sold' => $data['totalAnimals'],
                'Total Revenue' => number_format($data['totalRevenue'], 2),
            ],
        ])->download("sales-report-{$data['from']}-to-{$data['to']}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $data = $this->data($request);

        $rows = $data['rows']->map(fn ($r) => [
            $r->so_number, (string) $r->sale_date, $r->customer, $r->tag_id, $r->species,
            $r->sale_weight_kg, $r->price_per_kg, $r->line_total, $r->status,
        ]);

        return (new TableExport(
            $rows,
            ['SO #', 'Date', 'Customer', 'Tag', 'Species', 'Weight (kg)', 'Price/kg', 'Line Total', 'Status'],
            'Sales Report'
        ))->download("sales-report-{$data['from']}-to-{$data['to']}.xlsx");
    }

    private function data(Request $request): array
    {
        $from = $request->query('from') ?: now()->subMonths(3)->startOfMonth()->toDateString();
        $to = $request->query('to') ?: now()->toDateString();
        // Widen the upper bound to the end of the day -- a plain date string
        // would otherwise exclude same-day records timestamped after midnight.
        $toBound = "{$to} 23:59:59";

        $query = SalesOrderItem::query()
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
            ->join('animals', 'animals.id', '=', 'sales_order_items.animal_id')
            ->join('customers', 'customers.id', '=', 'sales_orders.customer_id')
            ->join('species', 'species.id', '=', 'animals.species_id')
            ->whereBetween('sales_orders.sale_date', [$from, $toBound]);

        if ($request->filled('customer_id')) {
            $query->where('sales_orders.customer_id', $request->integer('customer_id'));
        }
        if ($request->filled('species_id')) {
            $query->where('animals.species_id', $request->integer('species_id'));
        }
        if ($request->filled('status')) {
            $query->where('sales_orders.status', $request->string('status'));
        }

        $rows = $query->select([
            'sales_orders.so_number', 'sales_orders.sale_date', 'customers.name as customer',
            'animals.tag_id', 'species.name as species', 'sales_order_items.sale_weight_kg',
            'sales_order_items.price_per_kg', 'sales_order_items.line_total', 'sales_orders.status',
        ])->orderBy('sales_orders.sale_date')->get();

        return [
            'rows' => $rows,
            'totalRevenue' => (float) $rows->sum('line_total'),
            'totalAnimals' => $rows->count(),
            'customers' => Customer::orderBy('name')->get(),
            'speciesList' => Species::orderBy('name')->get(),
            'from' => $from,
            'to' => $to,
        ];
    }
}
