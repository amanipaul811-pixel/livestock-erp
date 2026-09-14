<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnimalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tag_id' => 'required|string|unique:animals,tag_id',
            'batch_id' => 'required|exists:batches,id',
            'species_id' => 'required|exists:species,id',
            'breed' => 'nullable|string',
            'sex' => 'required|in:male,female',
            'estimated_age_months' => 'nullable|integer',
            'entry_date' => 'required|date',
            'entry_weight_kg' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'current_pen_id' => 'nullable|exists:pens,id',
        ];
    }
}
