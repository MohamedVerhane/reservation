<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateHotelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        $hotelId = $this->route('hotel')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('hotels', 'slug')->ignore($hotelId)],
            'description' => ['nullable', 'string', 'max:5000'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'star_rating' => ['required', 'integer', 'between:1,5'],
            'is_active' => ['boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => __('validation-custom.hotel_name_required'),
            'email.required' => __('validation-custom.email_required'),
            'email.email' => __('validation-custom.email_valid'),
            'cover_image.image' => __('validation-custom.cover_image_image'),
            'cover_image.mimes' => __('validation-custom.cover_image_mimes'),
            'cover_image.max' => __('validation-custom.cover_image_max_size'),
            'star_rating.between' => __('validation-custom.star_rating_between'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('name') && empty($this->slug)) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }
}
