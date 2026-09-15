<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class RestockFeedItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity_kg' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:255',
        ];
    }
}
