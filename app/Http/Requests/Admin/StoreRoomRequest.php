<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'hotel_id' => ['required', 'exists:hotels,id'],
            'room_type_id' => [
                'required',
                'exists:room_types,id',
                Rule::exists('room_types', 'id')->where('hotel_id', $this->input('hotel_id')),
            ],
            'room_number' => ['required', 'string', 'max:20'],
            'floor' => ['nullable', 'integer', 'between:-5,100'],
            'status' => ['required', 'in:available,occupied,maintenance,out_of_order'],
            'is_active' => ['boolean'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['exists:amenities,id'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'hotel_id.required' => __('validation-custom.hotel_required'),
            'room_type_id.required' => __('validation-custom.room_type_required'),
            'room_type_id.exists' => __('validation-custom.room_type.invalid'),
            'room_number.required' => __('validation-custom.room_number_required'),
            'room_number.unique' => __('validation-custom.room_number_unique'),
            'status.in' => __('validation-custom.status_invalid'),
            'images.max' => __('validation-custom.images_max'),
            'images.*.image' => __('validation-custom.images_image'),
            'images.*.mimes' => __('validation-custom.images_mimes'),
            'images.*.max' => __('validation-custom.images_max_size'),
        ];
    }
}
