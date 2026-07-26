<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi guru dicek di controller (authorizeTeacher)
    }

    public function rules(): array
    {
        return [
            'title'              => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string', 'max:2000'],
            'order'              => ['required', 'integer', 'min:1'],
            'estimated_minutes'  => ['required', 'integer', 'min:1', 'max:300'],
            'status'             => ['required', 'in:draft,published'],
            'sequential_unlock'  => ['nullable', 'boolean'],
            'unlock_method'      => ['nullable', 'required_if:sequential_unlock,1', 'in:scroll,manual'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'             => 'Judul materi wajib diisi.',
            'order.required'             => 'Urutan tampil wajib diisi.',
            'estimated_minutes.required' => 'Estimasi waktu wajib diisi.',
            'estimated_minutes.max'      => 'Estimasi waktu maksimal 300 menit.',
        ];
    }
}
