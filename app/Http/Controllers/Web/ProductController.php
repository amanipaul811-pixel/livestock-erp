<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Animal;

class ProductController extends Controller
{
    // "Products" are the animals the farm actually has to offer right now --
    // still on feed, not yet sold, dead, or transferred out. Selling price is
    // computed live from weight x the species' default price/kg, the same
    // inputs the Record a Sale form already pre-fills from.
    public function index()
    {
        $animals = Animal::where('status', 'on_feed')
            ->with(['species', 'batch', 'healthRecords'])
            ->get()
            ->map(function (Animal $animal) {
                $weightKg = $animal->latestWeightKg();
                $pricePerKg = $animal->species->default_price_per_kg;
                $lastHealthRecord = $animal->healthRecords->sortByDesc('record_date')->first();

                return (object) [
                    'animal' => $animal,
                    'weight_kg' => $weightKg,
                    'price_per_kg' => $pricePerKg,
                    'selling_price' => $pricePerKg !== null ? $weightKg * $pricePerKg : null,
                    'ready_to_sell' => $animal->isReadyToSell(),
                    'last_health_record' => $lastHealthRecord,
                ];
            })
            ->sortBy(fn ($row) => $row->animal->tag_id)
            ->values();

        return view('products.index', ['products' => $animals]);
    }
}
