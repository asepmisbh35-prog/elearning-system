<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'topic'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'scheduled_at' => ['nullable', 'date'],
            'status'       => ['required', 'in:draft,published'],
        ];
    }

    public function messages(): array
    {
        return [
            'topic.required' => 'Topik pertemuan wajib diisi.',
        ];
    }
}
