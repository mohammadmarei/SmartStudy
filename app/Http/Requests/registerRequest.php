<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'       => 'required|string|max:255',
            'username'        => 'nullable|string|max:255|unique:users',
            'email'           => 'required|email|unique:users',
            'password'        => 'required|string|min:6|confirmed',
            'role_id'         => 'nullable|exists:roles,id',
            'agreed_to_terms' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'       => 'Full name is required.',
            'full_name.max'            => 'Full name must not exceed 255 characters.',
            'username.unique'          => 'This username is already taken.',
            'username.max'             => 'Username must not exceed 255 characters.',
            'email.required'           => 'Email address is required.',
            'email.email'              => 'Please provide a valid email address.',
            'email.unique'             => 'This email is already registered.',
            'password.required'        => 'Password is required.',
            'password.min'             => 'Password must be at least 6 characters.',
            'password.confirmed'       => 'Password confirmation does not match.',
            'role_id.exists'           => 'Selected role does not exist.',
            'agreed_to_terms.required' => 'You must agree to the terms.',
            'agreed_to_terms.accepted' => 'You must accept the terms and conditions.',
        ];
    }
}
