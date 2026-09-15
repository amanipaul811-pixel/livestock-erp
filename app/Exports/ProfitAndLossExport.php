<?php

namespace App\Exports;

use App\Exports\Sheets\AgingSheet;
use App\Exports\Sheets\BalanceSheetSnapshotSheet;
use App\Exports\Sheets\BatchProfitabilitySheet;
use App\Exports\Sheets\KeyValueSheet;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProfitAndLossExport implements Export, WithMultipleSheets
{
    use Exportable;

    public function __construct(private readonly array $data)
    {
    }

    public function sheets(): array
    {
        return [
            new BatchProfitabilitySheet($this->data['batchRows'], $this->data['totals']),
            new BalanceSheetSnapshotSheet(
                $this->data['wipValue'],
                $this->data['feedInventoryValue'],
                $this->data['totalReceivable'],
                $this->data['totalPayable'],
            ),
            new AgingSheet($this->data['accountsReceivable'], 'Customer', 'Invoice', 'Accounts Receivable Aging'),
            new AgingSheet($this->data['accountsPayable'], 'Supplier', 'PO', 'Accounts Payable Aging'),
            new KeyValueSheet($this->data['expenseByCategory'], 'Category', 'Expenses by Category'),
            new KeyValueSheet($this->data['revenueBySpecies'], 'Species', 'Revenue by Species'),
            new KeyValueSheet($this->data['revenueByCustomer'], 'Customer', 'Revenue by Customer'),
        ];
    }
}
