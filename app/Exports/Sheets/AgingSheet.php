<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

// Shared by the accounts receivable and accounts payable aging sheets --
// both are the same shape: party, reference, balance, age bucket.
class AgingSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(
        private readonly Collection $rows,
        private readonly string $partyHeading,
        private readonly string $referenceHeading,
        private readonly string $title
    ) {
    }

    public function collection(): Collection
    {
        return $this->rows->map(fn (array $row) => [
            $row['party'],
            $row['reference'],
            $row['balance'],
            $row['bucket'],
        ]);
    }

    public function headings(): array
    {
        return [$this->partyHeading, $this->referenceHeading, 'Balance', 'Age'];
    }

    public function title(): string
    {
        return $this->title;
    }
}
