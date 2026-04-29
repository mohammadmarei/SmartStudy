<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'full_name' => 'sometimes|string|max:255',
            'username'  => 'sometimes|string|max:255|unique:users,username,' . $userId,
            'email'     => 'sometimes|email|unique:users,email,' . $userId,
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.max'    => 'Full name must not exceed 255 characters.',
            'username.unique'  => 'This username is already taken.',
            'username.max'     => 'Username must not exceed 255 characters.',
            'email.email'      => 'Please provide a valid email address.',
            'email.unique'     => 'This email is already registered.',
        ];
    }
}
