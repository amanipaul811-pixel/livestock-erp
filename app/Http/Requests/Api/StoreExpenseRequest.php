<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => 'required|in:labor,utilities,transport,rent,other',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }
}
