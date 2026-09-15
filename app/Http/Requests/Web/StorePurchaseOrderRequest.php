<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'order_type' => 'required|in:animal,feed,medicine,other',
            'feed_item_id' => 'required_if:order_type,feed|nullable|exists:feed_items,id',
            'quantity_kg' => 'required_if:order_type,feed|nullable|numeric|min:0.01',
            'order_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
        ];
    }
}
