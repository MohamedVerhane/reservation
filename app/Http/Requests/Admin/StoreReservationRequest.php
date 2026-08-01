<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'hotel_id' => ['required', 'exists:hotels,id'],
            'room_id' => ['required', 'exists:rooms,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:20'],
            'children_count' => ['nullable', 'integer', 'min:0', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => __('validation-custom.guest_required'),
            'hotel_id.required' => __('validation-custom.hotel_required'),
            'room_id.required' => __('validation-custom.room_required'),
            'check_in.after_or_equal' => __('validation-custom.check_in_future_or_today'),
            'check_out.after' => __('validation-custom.check_out_after_check_in'),
            'guests.min' => __('validation-custom.adults_min'),
        ];
    }
}
