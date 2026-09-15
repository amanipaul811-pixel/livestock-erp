<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class BalanceSheetSnapshotSheet implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(
        private readonly float $wipValue,
        private readonly float $feedInventoryValue,
        private readonly float $totalReceivable,
        private readonly float $totalPayable,
    ) {
    }

    public function collection(): Collection
    {
        return collect([
            ['Livestock WIP Value', $this->wipValue],
            ['Feed Inventory Value', $this->feedInventoryValue],
            ['Accounts Receivable', $this->totalReceivable],
            ['Accounts Payable', $this->totalPayable],
        ]);
    }

    public function headings(): array
    {
        return ['Item', 'Value (as of today)'];
    }

    public function title(): string
    {
        return 'Balance Sheet Snapshot';
    }
}
