<?php

namespace App\Http\Requests\Web;

use Illuminate\Foundation\Http\FormRequest;

class StoreWeighInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'weigh_date' => 'required|date',
            'weight_kg' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }
}
