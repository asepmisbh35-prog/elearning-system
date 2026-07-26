<?php

namespace App\Http\Requests\Auth;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterSiswaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:255'],
            'nisn'       => ['required', 'string', 'digits:10', 'exists:students,nisn'],
            'birth_date' => ['required', 'date', 'before:today'],
            'email'      => ['required', 'email', 'unique:users,email', 'max:255'],
            'password'   => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Nama lengkap wajib diisi.',
            'nisn.required'       => 'NISN wajib diisi.',
            'nisn.digits'         => 'NISN harus berupa 10 digit angka.',
            'nisn.exists'         => 'NISN tidak ditemukan dalam data sekolah.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.before'   => 'Tanggal lahir tidak valid.',
            'email.required'      => 'Email wajib diisi.',
            'email.email'         => 'Format email tidak valid.',
            'email.unique'        => 'Email sudah digunakan oleh akun lain.',
            'password.required'   => 'Password wajib diisi.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'password.min'        => 'Password minimal 8 karakter.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $student = Student::where('nisn', $this->nisn)->first();

            if ($student && $student->has_registered) {
                $validator->errors()->add('nisn', 'NISN ini sudah terdaftar. Silakan login.');
            }
        });
    }
}
