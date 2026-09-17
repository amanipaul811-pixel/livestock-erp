<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AnimalMovement;
use App\Models\Batch;
use App\Models\HealthRecord;
use App\Models\PurchaseOrder;
use App\Models\SalesOrder;
use App\Models\WeighIn;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $month = $this->resolveMonth($request->query('month'));
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $endBound = $end->toDateString().' 23:59:59';

        $events = [];
        $add = function (string $date, string $type, string $label, ?string $url = null) use (&$events) {
            $events[$date][] = ['type' => $type, 'label' => $label, 'url' => $url];
        };

        foreach (SalesOrder::with('customer')->whereBetween('sale_date', [$start->toDateString(), $endBound])->get() as $so) {
            $add($so->sale_date->toDateString(), 'sale', "Sale to {$so->customer->name} (".number_format($so->total_amount, 2).')', route('sales-orders.show', $so));
        }

        foreach (PurchaseOrder::with('supplier')->whereBetween('order_date', [$start->toDateString(), $endBound])->get() as $po) {
            $add($po->order_date->toDateString(), 'purchase', "{$po->po_number}: {$po->supplier->name}", route('purchase-orders.show', $po));
        }

        foreach (AnimalMovement::with('animal')->whereBetween('move_date', [$start->toDateString(), $endBound])->get() as $m) {
            $add($m->move_date->toDateString(), 'movement', "{$m->animal->tag_id} moved pens", route('animals.show', $m->animal));
        }

        foreach (HealthRecord::with('animal')->whereBetween('record_date', [$start->toDateString(), $endBound])->get() as $h) {
            $add($h->record_date->toDateString(), 'health', "{$h->animal->tag_id}: {$h->record_type}", route('animals.show', $h->animal));
        }

        $weighInCounts = WeighIn::whereBetween('weigh_date', [$start->toDateString(), $endBound])
            ->selectRaw('weigh_date, count(*) as total')
            ->groupBy('weigh_date')
            ->get();
        foreach ($weighInCounts as $row) {
            $date = Carbon::parse($row->weigh_date)->toDateString();
            $add($date, 'weighin', "{$row->total} weigh-in(s) recorded", route('reports.movements'));
        }

        // Forward-looking: batches expected to finish this month, so the
        // calendar isn't purely a log of what already happened.
        foreach (Batch::whereBetween('expected_end_date', [$start->toDateString(), $end->toDateString()])->get() as $batch) {
            $add($batch->expected_end_date->toDateString(), 'batch_due', "{$batch->batch_code} expected ready", route('batches.show', $batch));
        }

        return view('calendar.index', [
            'month' => $month,
            'weeks' => $this->weeksFor($month),
            'events' => $events,
            'prevMonth' => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonth()->format('Y-m'),
        ]);
    }

    private function resolveMonth(?string $month): Carbon
    {
        if ($month && preg_match('/^\d{4}-\d{2}$/', $month)) {
            return Carbon::createFromFormat('Y-m-d', "{$month}-01")->startOfMonth();
        }

        return now()->startOfMonth();
    }

    /** @return array<int, array<int, Carbon>> */
    private function weeksFor(Carbon $month): array
    {
        $cursor = $month->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $last = $month->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $days = [];
        while ($cursor->lte($last)) {
            $days[] = $cursor->copy();
            $cursor->addDay();
        }

        return array_chunk($days, 7);
    }
}
