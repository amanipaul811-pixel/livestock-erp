<?php

namespace Database\Seeders;

use App\Models\Pen;
use Illuminate\Database\Seeder;

class PenSeeder extends Seeder
{
    public function run(): void
    {
        $pens = [
            ['name' => 'Quarantine 1', 'capacity' => 20, 'stage' => 'quarantine'],
            ['name' => 'Growing 1', 'capacity' => 50, 'stage' => 'growing'],
            ['name' => 'Growing 2', 'capacity' => 50, 'stage' => 'growing'],
            ['name' => 'Finishing 1', 'capacity' => 40, 'stage' => 'finishing'],
            ['name' => 'Finishing 2', 'capacity' => 40, 'stage' => 'finishing'],
        ];

        foreach ($pens as $pen) {
            Pen::firstOrCreate(['name' => $pen['name']], $pen);
        }
    }
}
