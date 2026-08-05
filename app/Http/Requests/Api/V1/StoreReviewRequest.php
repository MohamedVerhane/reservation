<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Reservation;
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
            'hotel_id'       => ['required', 'exists:hotels,id'],
            'reservation_id' => ['nullable', 'integer', 'exists:reservations,id', Rule::unique('reviews', 'reservation_id')],
            'rating'         => ['required', 'integer', 'min:1', 'max:5'],
            'comment'        => ['nullable', 'string', 'max:2000'],
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

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $reservationId = $this->input('reservation_id');

            if ($reservationId === null) {
                return;
            }

            $owned = Reservation::where('id', $reservationId)
                ->where('user_id', $this->user()?->id)
                ->whereIn('status', ['completed', 'checked_out', 'confirmed'])
                ->exists();

            if (! $owned) {
                $validator->errors()->add(
                    'reservation_id',
                    __('validation-custom.rating_reservation_not_owned')
                );
            }
        });
    }
}
