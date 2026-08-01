<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoomTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $roomType = $this->route('roomType');

        return [
            'hotel_id' => ['required', 'exists:hotels,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'base_price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'max_guests' => ['required', 'integer', 'between:1,20'],
            'max_children' => ['nullable', 'integer', 'between:0,10'],
            'is_active' => ['boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'hotel_id.required' => __('validation-custom.hotel_required'),
            'hotel_id.exists' => __('validation-custom.hotel_exists'),
            'name.required' => __('validation-custom.room_type_name_required'),
            'base_price.required' => __('validation-custom.price_required'),
            'base_price.numeric' => __('validation-custom.price_numeric'),
            'base_price.min' => __('validation-custom.price_min'),
            'max_guests.required' => __('validation-custom.max_guests_required'),
            'max_guests.between' => __('validation-custom.max_guests_between'),
        ];
    }
}
