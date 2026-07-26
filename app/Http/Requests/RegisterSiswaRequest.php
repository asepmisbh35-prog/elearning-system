<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;


class RegisterSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'nisn'         => ['required', 'string', 'size:10'],
            'birth_date'   => ['required', 'date', 'before:today'],
            'email'        => ['required', 'email', 'unique:users,email'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'address'      => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'nisn.required'         => 'NISN wajib diisi.',
            'nisn.size'             => 'NISN harus tepat 10 digit.',
            'birth_date.required'   => 'Tanggal lahir wajib diisi.',
            'birth_date.before'     => 'Tanggal lahir tidak valid.',
            'email.required'        => 'Email wajib diisi.',
            'email.unique'          => 'Email sudah digunakan.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ];
    }
}

