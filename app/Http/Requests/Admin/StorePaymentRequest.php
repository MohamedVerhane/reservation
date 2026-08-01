<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reservation_id' => ['required', 'exists:reservations,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'method' => ['required', Rule::in(['cash', 'credit_card', 'debit_card', 'bank_transfer', 'online'])],
            'transaction_id' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'reservation_id.required' => __('validation-custom.reservation_required'),
            'amount.required' => __('validation-custom.amount_required'),
            'amount.min' => __('validation-custom.amount_min'),
            'method.required' => __('validation-custom.payment_method_required'),
        ];
    }
}
