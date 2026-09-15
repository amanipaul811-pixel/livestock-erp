<?php

namespace App\Http\Requests\Api;

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
            // The status=on_feed constraint blocks re-selling an animal
            // that's already sold/dead/transferred -- nothing else in the
            // stack checked this, so a repeated or stale-UI submission
            // could otherwise double-count revenue on the same animal.
            'items.*.animal_id' => 'required|distinct|exists:animals,id,status,on_feed',
            'items.*.sale_weight_kg' => 'required|numeric|min:0',
            'items.*.price_per_kg' => 'required|numeric|min:0',
        ];
    }
}
