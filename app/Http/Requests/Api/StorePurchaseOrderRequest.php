<?php

namespace App\Http\Requests\Api;

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
            'order_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
        ];
    }
}
