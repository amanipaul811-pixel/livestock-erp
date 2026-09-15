<?php

namespace App\Http\Requests\Api;

use Closure;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'in:pending,received,cancelled',
                // received/cancelled are terminal -- nothing else validated
                // that, so a purchase order could otherwise be flipped back
                // to "pending" after being marked received, or "received"
                // after being cancelled.
                function (string $attribute, mixed $value, Closure $fail) {
                    $current = $this->route('purchaseOrder')->status;

                    if (in_array($current, ['received', 'cancelled']) && $value !== $current) {
                        $fail("This purchase order is already {$current} and can't be changed.");
                    }
                },
            ],
        ];
    }
}
