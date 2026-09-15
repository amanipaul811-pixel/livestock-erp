<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class BatchProfitabilitySheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private readonly Collection $batchRows, private readonly array $totals)
    {
    }

    public function collection(): Collection
    {
        $rows = $this->batchRows->map(fn (array $row) => [
            $row['batch']->batch_code,
            $row['batch']->status,
            $row['revenue'],
            $row['purchase_cost'],
            $row['feed_cost'],
            $row['health_cost'],
            $row['other_expenses'],
            $row['net_profit'],
        ]);

        $rows->push([
            'TOTAL',
            '',
            $this->totals['revenue'],
            $this->totals['purchase_cost'],
            $this->totals['feed_cost'],
            $this->totals['health_cost'],
            $this->totals['other_expenses'] + $this->totals['overhead_expenses'],
            $this->totals['net_profit'],
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return ['Batch', 'Status', 'Revenue', 'Purchase Cost', 'Feed Cost', 'Health Cost', 'Other Expenses', 'Net Profit'];
    }

    public function title(): string
    {
        return 'Batch Profitability';
    }
}
