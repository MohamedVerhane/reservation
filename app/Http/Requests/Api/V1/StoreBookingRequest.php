<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_id'       => ['required', 'exists:hotels,id'],
            'room_id'        => ['required', 'exists:rooms,id'],
            'check_in'       => ['required', 'date', 'after_or_equal:today'],
            'check_out'      => ['required', 'date', 'after:check_in'],
            'adults'         => ['required', 'integer', 'min:1', 'max:20'],
            'children'       => ['nullable', 'integer', 'min:0', 'max:10'],
            'payment_method' => ['required', 'string', Rule::in(['credit_card', 'debit_card', 'bank_transfer', 'cash', 'online'])],
            'special_requests' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_id.required'     => __('validation-custom.hotel_required'),
            'hotel_id.exists'       => __('validation-custom.hotel_exists'),
            'room_id.required'      => __('validation-custom.room_required'),
            'room_id.exists'        => __('validation-custom.room_id_exists'),
            'check_in.required'     => __('validation-custom.check_in_required'),
            'check_in.after_or_equal' => __('validation-custom.check_in_future_or_today'),
            'check_out.required'    => __('validation-custom.check_out_required'),
            'check_out.after'       => __('validation-custom.check_out_after_check_in'),
            'adults.required'       => __('validation-custom.adults_required'),
            'adults.min'            => __('validation-custom.adults_min'),
            'payment_method.required' => __('validation-custom.payment_method_required'),
            'payment_method.in'     => __('validation-custom.payment_method_invalid'),
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $room = Room::find($this->input('room_id'));

            if ($room && (int) $room->hotel_id !== (int) $this->input('hotel_id')) {
                $validator->errors()->add(
                    'room_id',
                    __('validation-custom.room_hotel_mismatch')
                );
            }
        });
    }
}
