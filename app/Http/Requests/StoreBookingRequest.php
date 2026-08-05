<?php

namespace App\Http\Requests;

use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|\Illuminate\Validation\Rules\Rule>>
     */
    public function rules(): array
    {
        return [
            'hotel_id'         => ['required', 'exists:hotels,id'],
            'room_id'          => ['required', 'exists:rooms,id'],
            'check_in'         => ['required', 'date', 'after_or_equal:today'],
            'check_out'        => ['required', 'date', 'after:check_in'],
            'adults'           => ['required', 'integer', 'min:1', 'max:20'],
            'children'         => ['nullable', 'integer', 'min:0', 'max:10'],
            'special_requests' => ['nullable', 'string', 'max:1000'],
            'payment_method'   => ['required', Rule::in(['credit_card', 'debit_card', 'cash', 'online'])],
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'hotel_id.required'       => __('validation-custom.hotel_required'),
            'hotel_id.exists'         => __('validation-custom.hotel_exists'),
            'room_id.required'        => __('validation-custom.room_required'),
            'room_id.exists'          => __('validation-custom.room_id_exists'),
            'check_in.required'       => __('validation-custom.check_in_required'),
            'check_in.after_or_equal' => __('validation-custom.check_in_future_or_today'),
            'check_out.required'      => __('validation-custom.check_out_required'),
            'check_out.after'         => __('validation-custom.check_out_after_check_in'),
            'adults.required'         => __('validation-custom.adults_required'),
            'adults.min'              => __('validation-custom.adults_min'),
            'adults.max'              => __('validation-custom.adults_max'),
            'children.min'            => __('validation-custom.children_min'),
            'children.max'            => __('validation-custom.children_max'),
            'special_requests.max'    => __('validation-custom.special_requests_max'),
            'payment_method.required' => __('validation-custom.payment_method_required'),
            'payment_method.in'       => __('validation-custom.payment_method_invalid'),
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->any()) {
                return;
            }

            $roomId = $this->input('room_id');
            $checkIn = Carbon::parse($this->input('check_in'));
            $checkOut = Carbon::parse($this->input('check_out'));

            $room = Room::where('id', $roomId)
                ->where('is_active', true)
                ->first();

            if (!$room) {
                return;
            }

            $isAvailable = Room::where('id', $roomId)
                ->where('is_active', true)
                ->where('status', '!=', 'out_of_order')
                ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                    $q->where('status', '!=', 'cancelled')
                        ->where('check_in', '<', $checkOut)
                        ->where('check_out', '>', $checkIn);
                })
                ->exists();

            if (!$isAvailable) {
                $validator->errors()->add(
                    'booking',
                    'This room is no longer available for the selected dates. Please choose another room.'
                );
            }

            if ($room && (int) $room->hotel_id !== (int) $this->input('hotel_id')) {
                $validator->errors()->add(
                    'room_id',
                    __('validation-custom.room_hotel_mismatch')
                );
            }
        });
    }
}
