<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'unit' => 'required|in:kg,bag,liter',
            'cost_per_unit' => 'required|numeric|min:0',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'reorder_level' => 'nullable|numeric|min:0',
        ];
    }
}
