<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date_of_birth' => 'nullable|date|before:today',
            'gender'        => 'nullable|in:male,female',
            'bio'           => 'nullable|string|max:500',
            'linkedin_url'  => 'nullable|url|max:255',
            'github_url'    => 'nullable|url|max:255',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before'  => 'Date of birth must be before today.',
            'date_of_birth.date'    => 'Please provide a valid date.',
            'gender.in'             => 'Invalid gender value.',
            'bio.max'               => 'Bio must not exceed 500 characters.',
            'linkedin_url.url'      => 'Please provide a valid LinkedIn URL.',
            'linkedin_url.max'      => 'LinkedIn URL must not exceed 255 characters.',
            'github_url.url'        => 'Please provide a valid GitHub URL.',
            'github_url.max'        => 'GitHub URL must not exceed 255 characters.',
            'avatar.image'          => 'Avatar must be an image.',
            'avatar.mimes'          => 'Avatar must be jpg, jpeg, png, or webp.',
            'avatar.max'            => 'Avatar size must not exceed 2MB.',
        ];
    }
}
