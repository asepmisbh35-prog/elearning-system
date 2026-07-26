<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'score'    => ['required', 'integer', 'min:0'],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
