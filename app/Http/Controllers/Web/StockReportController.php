<?php

namespace App\Http\Controllers\Web;

use App\Exports\TableExport;
use App\Http\Controllers\Controller;
use App\Models\FeedItem;
use App\Models\FeedStockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class StockReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.stock', $this->data($request));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->data($request);

        return Pdf::loadView('reports.table-pdf', [
            'title' => 'Stock Movement Report',
            'subtitle' => "{$data['from']} to {$data['to']}",
            'headings' => $data['runningBalance'] !== null
                ? ['Date', 'Feed Item', 'Type', 'Quantity (kg)', 'Balance After', 'Reason']
                : ['Date', 'Feed Item', 'Type', 'Quantity (kg)', 'Reason'],
            'rows' => $data['rows']->map(fn (array $r) => $data['runningBalance'] !== null
                ? [$r['movement']->occurred_at->format('Y-m-d'), $r['movement']->feedItem->name, $r['movement']->type, number_format($r['movement']->quantity_kg, 2), number_format($r['balance'], 2), $r['movement']->reason]
                : [$r['movement']->occurred_at->format('Y-m-d'), $r['movement']->feedItem->name, $r['movement']->type, number_format($r['movement']->quantity_kg, 2), $r['movement']->reason]),
            'summary' => array_filter([
                'Total In (kg)' => number_format($data['totalIn'], 2),
                'Total Out (kg)' => number_format($data['totalOut'], 2),
                'Net Adjustment (kg)' => number_format($data['totalAdjustment'], 2),
                'Current Stock (kg)' => $data['currentStock'] !== null ? number_format($data['currentStock'], 2) : null,
            ]),
        ])->download("stock-report-{$data['from']}-to-{$data['to']}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $data = $this->data($request);

        $headings = $data['runningBalance'] !== null
            ? ['Date', 'Feed Item', 'Type', 'Quantity (kg)', 'Balance After', 'Reason']
            : ['Date', 'Feed Item', 'Type', 'Quantity (kg)', 'Reason'];

        $rows = $data['rows']->map(fn (array $r) => $data['runningBalance'] !== null
            ? [$r['movement']->occurred_at->format('Y-m-d'), $r['movement']->feedItem->name, $r['movement']->type, $r['movement']->quantity_kg, $r['balance'], $r['movement']->reason]
            : [$r['movement']->occurred_at->format('Y-m-d'), $r['movement']->feedItem->name, $r['movement']->type, $r['movement']->quantity_kg, $r['movement']->reason]);

        return (new TableExport($rows, $headings, 'Stock Movement Report'))
            ->download("stock-report-{$data['from']}-to-{$data['to']}.xlsx");
    }

    private function data(Request $request): array
    {
        $from = $request->query('from') ?: now()->subMonths(3)->startOfMonth()->toDateString();
        $to = $request->query('to') ?: now()->toDateString();
        $toBound = "{$to} 23:59:59";

        $query = FeedStockMovement::with('feedItem')->whereBetween('occurred_at', [$from, $toBound]);

        $feedItemId = $request->filled('feed_item_id') ? $request->integer('feed_item_id') : null;
        if ($feedItemId) {
            $query->where('feed_item_id', $feedItemId);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $movements = $query->orderBy('occurred_at')->get();

        $runningBalance = null;
        $rows = $movements->map(fn (FeedStockMovement $m) => ['movement' => $m]);

        if ($feedItemId) {
            $openingBalance = (float) FeedStockMovement::where('feed_item_id', $feedItemId)
                ->where('occurred_at', '<', $from)
                ->get()
                ->sum(fn (FeedStockMovement $m) => match ($m->type) {
                    'in', 'adjustment' => $m->quantity_kg,
                    'out' => -$m->quantity_kg,
                });

            $running = $openingBalance;
            $rows = $movements->map(function (FeedStockMovement $m) use (&$running) {
                $running += match ($m->type) {
                    'in', 'adjustment' => $m->quantity_kg,
                    'out' => -$m->quantity_kg,
                };

                return ['movement' => $m, 'balance' => $running];
            });
            $runningBalance = $running;
        }

        return [
            'rows' => $rows,
            'totalIn' => (float) $movements->where('type', 'in')->sum('quantity_kg'),
            'totalOut' => (float) $movements->where('type', 'out')->sum('quantity_kg'),
            'totalAdjustment' => (float) $movements->where('type', 'adjustment')->sum('quantity_kg'),
            'runningBalance' => $runningBalance,
            'currentStock' => $feedItemId ? FeedItem::find($feedItemId)?->currentStock() : null,
            'feedItems' => FeedItem::orderBy('name')->get(),
            'from' => $from,
            'to' => $to,
        ];
    }
}
