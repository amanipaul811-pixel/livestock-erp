<?php

namespace App\Http\Controllers\Web;

use App\Exports\TableExport;
use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\HealthRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class HealthReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.health', $this->data($request));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->data($request);

        return Pdf::loadView('reports.table-pdf', [
            'title' => 'Health Report',
            'subtitle' => "{$data['from']} to {$data['to']}",
            'headings' => ['Date', 'Tag', 'Batch', 'Type', 'Description', 'Medicine', 'Cost', 'Performed By'],
            'rows' => $data['rows']->map(fn (HealthRecord $r) => [
                $r->record_date->format('Y-m-d'), $r->animal->tag_id, $r->animal->batch->batch_code,
                $r->record_type, $r->description ?? '—', $r->medicine_used ?? '—',
                number_format($r->cost, 2), $r->performedBy->full_name ?? '—',
            ]),
            'summary' => array_merge(
                ['Total Cost' => number_format($data['totalCost'], 2), 'Records' => $data['rows']->count()],
                $data['countByType']->mapWithKeys(fn ($count, $type) => [ucfirst($type) => $count])->toArray()
            ),
        ])->download("health-report-{$data['from']}-to-{$data['to']}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $data = $this->data($request);

        $rows = $data['rows']->map(fn (HealthRecord $r) => [
            $r->record_date->format('Y-m-d'), $r->animal->tag_id, $r->animal->batch->batch_code,
            $r->record_type, $r->description ?? '', $r->medicine_used ?? '',
            $r->cost, $r->performedBy->full_name ?? '',
        ]);

        return (new TableExport(
            $rows,
            ['Date', 'Tag', 'Batch', 'Type', 'Description', 'Medicine', 'Cost', 'Performed By'],
            'Health Report'
        ))->download("health-report-{$data['from']}-to-{$data['to']}.xlsx");
    }

    private function data(Request $request): array
    {
        $from = $request->query('from') ?: now()->subMonths(3)->startOfMonth()->toDateString();
        $to = $request->query('to') ?: now()->toDateString();
        $toBound = "{$to} 23:59:59";

        $query = HealthRecord::with(['animal.batch', 'performedBy'])
            ->whereBetween('record_date', [$from, $toBound]);

        if ($request->filled('batch_id')) {
            $batchId = $request->integer('batch_id');
            $query->whereHas('animal', fn ($q) => $q->where('batch_id', $batchId));
        }
        if ($request->filled('tag')) {
            $tag = $request->string('tag');
            $query->whereHas('animal', fn ($q) => $q->where('tag_id', 'like', "%{$tag}%"));
        }
        if ($request->filled('record_type')) {
            $query->where('record_type', $request->string('record_type'));
        }

        $rows = $query->orderBy('record_date')->get();

        return [
            'rows' => $rows,
            'totalCost' => (float) $rows->sum('cost'),
            'countByType' => $rows->countBy('record_type'),
            'batches' => Batch::orderBy('batch_code')->get(),
            'from' => $from,
            'to' => $to,
        ];
    }
}
