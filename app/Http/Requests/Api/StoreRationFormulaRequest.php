<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreRationFormulaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'species_id' => 'required|exists:species,id',
            'stage' => 'required|in:starter,growing,finishing',
            'items' => 'required|array|min:1',
            'items.*.feed_item_id' => 'required|exists:feed_items,id',
            'items.*.quantity_kg_per_head' => 'required|numeric|min:0.01',
        ];
    }
}
