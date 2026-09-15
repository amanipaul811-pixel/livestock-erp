<?php

namespace App\Http\Controllers\Web;

use App\Exports\TableExport;
use App\Http\Controllers\Controller;
use App\Models\AnimalMovement;
use App\Models\Batch;
use App\Models\Pen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AnimalMovementReportController extends Controller
{
    public function index(Request $request)
    {
        return view('reports.movements', $this->data($request));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->data($request);

        return Pdf::loadView('reports.table-pdf', [
            'title' => 'Animal Movement Report',
            'subtitle' => "{$data['from']} to {$data['to']}",
            'headings' => ['Date', 'Tag', 'Species', 'Batch', 'From Pen', 'To Pen', 'Reason'],
            'rows' => $data['rows']->map(fn (AnimalMovement $m) => [
                $m->move_date->format('Y-m-d'), $m->animal->tag_id, $m->animal->species->name,
                $m->animal->batch->batch_code, $m->fromPen->name ?? '—', $m->toPen->name ?? '—', $m->reason ?? '—',
            ]),
            'summary' => ['Total Movements' => $data['rows']->count()],
        ])->download("movements-report-{$data['from']}-to-{$data['to']}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $data = $this->data($request);

        $rows = $data['rows']->map(fn (AnimalMovement $m) => [
            $m->move_date->format('Y-m-d'), $m->animal->tag_id, $m->animal->species->name,
            $m->animal->batch->batch_code, $m->fromPen->name ?? '', $m->toPen->name ?? '', $m->reason ?? '',
        ]);

        return (new TableExport(
            $rows,
            ['Date', 'Tag', 'Species', 'Batch', 'From Pen', 'To Pen', 'Reason'],
            'Animal Movement Report'
        ))->download("movements-report-{$data['from']}-to-{$data['to']}.xlsx");
    }

    private function data(Request $request): array
    {
        $from = $request->query('from') ?: now()->subMonths(3)->startOfMonth()->toDateString();
        $to = $request->query('to') ?: now()->toDateString();
        $toBound = "{$to} 23:59:59";

        $query = AnimalMovement::with(['animal.species', 'animal.batch', 'fromPen', 'toPen'])
            ->whereBetween('move_date', [$from, $toBound]);

        if ($request->filled('pen_id')) {
            $penId = $request->integer('pen_id');
            $query->where(fn ($q) => $q->where('from_pen_id', $penId)->orWhere('to_pen_id', $penId));
        }
        if ($request->filled('batch_id')) {
            $batchId = $request->integer('batch_id');
            $query->whereHas('animal', fn ($q) => $q->where('batch_id', $batchId));
        }
        if ($request->filled('tag')) {
            $tag = $request->string('tag');
            $query->whereHas('animal', fn ($q) => $q->where('tag_id', 'like', "%{$tag}%"));
        }

        return [
            'rows' => $query->orderBy('move_date')->get(),
            'pens' => Pen::orderBy('name')->get(),
            'batches' => Batch::orderBy('batch_code')->get(),
            'from' => $from,
            'to' => $to,
        ];
    }
}
