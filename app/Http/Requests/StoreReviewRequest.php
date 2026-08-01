<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating'         => ['required', 'integer', 'min:1', 'max:5'],
            'comment'        => ['nullable', 'string', 'max:2000'],
            'reservation_id' => [
                'nullable',
                'integer',
                'exists:reservations,id',
                Rule::unique('reviews', 'reservation_id'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required'       => __('validation-custom.rating_required'),
            'rating.min'            => __('validation-custom.rating_min'),
            'rating.max'            => __('validation-custom.rating_max'),
            'reservation_id.unique' => __('validation-custom.rating_already_submitted'),
        ];
    }
}
