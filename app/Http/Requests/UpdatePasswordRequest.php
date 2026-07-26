<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'new_password'     => [
                'required',
                'string',
                'min:8',
                'confirmed',          // membutuhkan field new_password_confirmation
                'different:current_password',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'  => 'Password lama harus diisi.',
            'new_password.required'      => 'Password baru harus diisi.',
            'new_password.min'           => 'Password baru minimal 8 karakter.',
            'new_password.confirmed'     => 'Konfirmasi password tidak cocok.',
            'new_password.different'     => 'Password baru harus berbeda dari password lama.',
        ];
    }
}
