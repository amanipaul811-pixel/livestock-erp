<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreSalesOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.animal_id' => 'required|exists:animals,id',
            'items.*.sale_weight_kg' => 'required|numeric|min:0',
            'items.*.price_per_kg' => 'required|numeric|min:0',
        ];
    }
}
