<?php
// ============================================================
// File: app/Http/Requests/Admin/StoreGuruRequest.php
// ============================================================

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'phone'    => ['nullable', 'string', 'max:20'],
            'nip'      => ['nullable', 'string', 'max:20', 'unique:teachers,nip'],
            'subject'  => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Nama guru wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique'   => 'Email sudah digunakan.',
            'nip.unique'     => 'NIP sudah terdaftar.',
        ];
    }
}
