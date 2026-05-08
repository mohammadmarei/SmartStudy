<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAiSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject_id' => 'required|integer|exists:subjects,id',
            'material_id' => 'required|integer|exists:files,id',
            'key_points' => 'nullable|integer|min:1|max:25',
            'model' => 'nullable|string|max:100',
        ];
    }
}

