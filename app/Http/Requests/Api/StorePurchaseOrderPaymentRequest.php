<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $balanceDue = $this->route('purchaseOrder')->balanceDue();

        return [
            'payment_date' => 'required|date',
            'amount' => "required|numeric|min:0.01|max:{$balanceDue}",
            'method' => 'required|in:cash,bank_transfer,mobile_money,cheque',
            'notes' => 'nullable|string',
        ];
    }
}
