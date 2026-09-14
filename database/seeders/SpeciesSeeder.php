<?php

namespace Database\Seeders;

use App\Models\Species;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'name' => 'Cattle',
                'default_cycle_days' => 120,
                'target_adg_kg' => 1.20,
                'target_entry_weight_kg' => 250,
                'target_exit_weight_kg' => 400,
            ],
            [
                'name' => 'Goat',
                'default_cycle_days' => 90,
                'target_adg_kg' => 0.15,
                'target_entry_weight_kg' => 15,
                'target_exit_weight_kg' => 30,
            ],
            [
                'name' => 'Sheep',
                'default_cycle_days' => 90,
                'target_adg_kg' => 0.20,
                'target_entry_weight_kg' => 20,
                'target_exit_weight_kg' => 40,
            ],
        ];

        foreach ($defaults as $species) {
            Species::firstOrCreate(['name' => $species['name']], $species);
        }
    }
}
