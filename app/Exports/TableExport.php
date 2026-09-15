<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

// Generic single-sheet export for the simpler operational reports (sales,
// purchases, stock, movements, health) -- each is just a filtered table, so
// one reusable class beats five near-identical sheet classes.
class TableExport implements Export, FromCollection, WithHeadings, WithTitle
{
    use Exportable;

    public function __construct(
        private readonly Collection $rows,
        private readonly array $headings,
        private readonly string $title
    ) {
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return $this->title;
    }
}
