<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeedLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'feed_item_id' => 'required|exists:feed_items,id',
            'feed_date' => 'required|date',
            'quantity_kg' => 'required|numeric|min:0',
        ];
    }
}
