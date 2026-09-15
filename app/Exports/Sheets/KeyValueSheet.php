<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

// Generic "label -> total" sheet, reused for the expense-by-category and
// revenue-by-species/customer breakdowns in the P&L export.
class KeyValueSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(
        private readonly Collection $data,
        private readonly string $labelHeading,
        private readonly string $title
    ) {
    }

    public function collection(): Collection
    {
        return $this->data->map(fn ($total, $label) => [$label, $total])->values();
    }

    public function headings(): array
    {
        return [$this->labelHeading, 'Total'];
    }

    public function title(): string
    {
        return $this->title;
    }
}
