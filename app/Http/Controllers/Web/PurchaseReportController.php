<?php

namespace App\Http\Controllers\Web;

use App\Exports\TableExport;
use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.purchases', $this->data($request));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->data($request);

        return Pdf::loadView('reports.table-pdf', [
            'title' => 'Purchases Report',
            'subtitle' => "{$data['from']} to {$data['to']}",
            'headings' => ['PO #', 'Date', 'Supplier', 'Type', 'Total', 'Paid', 'Balance', 'Status'],
            'rows' => $data['rows']->map(fn (PurchaseOrder $po) => [
                $po->po_number, $po->order_date->format('Y-m-d'), $po->supplier->name, $po->order_type,
                number_format($po->total_amount, 2), number_format($po->amountPaid(), 2),
                number_format($po->balanceDue(), 2), $po->status,
            ]),
            'summary' => [
                'Purchase Orders' => $data['rows']->count(),
                'Total Ordered' => number_format($data['totalOrdered'], 2),
                'Total Paid' => number_format($data['totalPaid'], 2),
                'Total Outstanding' => number_format($data['totalOutstanding'], 2),
            ],
        ])->download("purchases-report-{$data['from']}-to-{$data['to']}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $data = $this->data($request);

        $rows = $data['rows']->map(fn (PurchaseOrder $po) => [
            $po->po_number, $po->order_date->format('Y-m-d'), $po->supplier->name, $po->order_type,
            $po->total_amount, $po->amountPaid(), $po->balanceDue(), $po->status,
        ]);

        return (new TableExport(
            $rows,
            ['PO #', 'Date', 'Supplier', 'Type', 'Total', 'Paid', 'Balance', 'Status'],
            'Purchases Report'
        ))->download("purchases-report-{$data['from']}-to-{$data['to']}.xlsx");
    }

    private function data(Request $request): array
    {
        $from = $request->query('from') ?: now()->subMonths(3)->startOfMonth()->toDateString();
        $to = $request->query('to') ?: now()->toDateString();
        $toBound = "{$to} 23:59:59";

        $query = PurchaseOrder::with('supplier')->whereBetween('order_date', [$from, $toBound]);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->integer('supplier_id'));
        }
        if ($request->filled('order_type')) {
            $query->where('order_type', $request->string('order_type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $rows = $query->orderBy('order_date')->get();

        return [
            'rows' => $rows,
            'totalOrdered' => (float) $rows->sum('total_amount'),
            'totalPaid' => (float) $rows->sum(fn (PurchaseOrder $po) => $po->amountPaid()),
            'totalOutstanding' => (float) $rows->sum(fn (PurchaseOrder $po) => $po->balanceDue()),
            'suppliers' => Supplier::orderBy('name')->get(),
            'from' => $from,
            'to' => $to,
        ];
    }
}
