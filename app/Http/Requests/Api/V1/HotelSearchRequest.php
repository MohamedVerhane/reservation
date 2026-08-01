<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HotelSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'    => ['nullable', 'string', 'max:255'],
            'city'      => ['nullable', 'string', 'max:255'],
            'star'      => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort'      => ['nullable', 'string', Rule::in(['name', 'rating', 'newest', 'price'])],
            'per_page'  => ['nullable', 'integer', 'min:1', 'max:50'],
        ];
    }
}
