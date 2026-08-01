<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hotel_id' => ['required', 'exists:hotels,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'images' => ['nullable', 'array', 'max:20'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'hotel_id.required' => __('validation-custom.hotel_required'),
            'title.required' => __('validation-custom.title_required'),
            'images.max' => __('validation-custom.images_max'),
            'images.*.mimes' => __('validation-custom.images_mimes'),
            'images.*.max' => __('validation-custom.images_max_size'),
        ];
    }
}
