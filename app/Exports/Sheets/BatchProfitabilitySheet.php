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
            $row['realized_revenue'],
            $row['realized_cogs'],
            $row['mortality_loss'],
            $row['wip_value'],
            $row['net_profit'],
        ]);

        $rows->push([
            'TOTAL',
            '',
            $this->totals['revenue'],
            $this->totals['cogs'] + $this->totals['overhead_expenses'],
            $this->totals['mortality_loss'],
            '',
            $this->totals['net_profit'],
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return ['Batch', 'Status', 'Revenue', 'COGS', 'Mortality Loss', 'WIP Value', 'Net Profit'];
    }

    public function title(): string
    {
        return 'Batch Profitability';
    }
}
