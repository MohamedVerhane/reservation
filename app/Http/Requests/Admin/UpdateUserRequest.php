<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')?->id),
            ],
            'phone' => ['nullable', 'string', 'max:255'],
            'role' => ['required', Rule::in(['admin', 'owner', 'guest'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('validation-custom.name.required'),
            'email.required' => __('validation-custom.email.required'),
            'email.unique' => __('validation-custom.email.unique'),
            'role.required' => __('validation-custom.role.required'),
            'password.min' => __('validation-custom.password.min'),
            'password.confirmed' => __('validation-custom.password.confirmed'),
        ];
    }
}
