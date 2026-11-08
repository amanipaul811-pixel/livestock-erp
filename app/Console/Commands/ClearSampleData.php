<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearSampleData extends Command
{
    protected $signature = 'app:clear-sample-data
                            {--keep-master-data : Keep suppliers, customers, warehouses, feed items and ration formulas}
                            {--force : Skip the typed confirmation}';

    protected $description = 'Delete all entered records and master data, keeping logins, roles, species and pens';

    // Child tables come before the tables they reference, so deletes never hit a foreign key.
    private const RECORDS = [
        'sales_order_items', 'payments', 'sales_orders', 'animal_movements', 'health_records',
        'weigh_ins', 'animals', 'feed_logs', 'feed_stock_movements', 'expenses',
        'purchase_orders', 'batches', 'notifications',
    ];

    private const MASTER_DATA = [
        'ration_formula_items', 'ration_formulas', 'feed_items', 'warehouses', 'suppliers', 'customers',
    ];

    public function handle(): int
    {
        $tables = array_values(array_filter(
            array_merge(self::RECORDS, $this->option('keep-master-data') ? [] : self::MASTER_DATA),
            fn (string $table) => Schema::hasTable($table),
        ));

        $this->components->warn('This permanently deletes the rows below from database "'.DB::connection()->getDatabaseName().'" ('.config('app.env').').');
        $this->table(['Table', 'Rows to delete'], array_map(fn (string $t) => [$t, DB::table($t)->count()], $tables));
        $this->line('Kept: users, roles, permissions, species, pens'.($this->option('keep-master-data') ? ', suppliers, customers, warehouses, feed items, ration formulas.' : '.'));

        if (! $this->option('force') && $this->ask('Type DELETE to confirm') !== 'DELETE') {
            $this->components->info('Nothing was deleted.');

            return self::FAILURE;
        }

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        $this->components->info('Done. Numbering (PO, SO, batch, tag) starts again from 1.');

        return self::SUCCESS;
    }
}
