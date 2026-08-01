<?php

namespace App\Http\Requests\Api\V1;

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
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'method'         => ['required', 'string', Rule::in(['credit_card', 'debit_card', 'bank_transfer', 'cash', 'online'])],
        ];
    }
}
