<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'              => ['required', 'string', 'max:255'],
            'content'            => ['required', 'string'],
            'image'              => ['nullable', 'image', 'max:5120'],
            'target_type'        => ['required', 'in:all,class,role,specific'],
            'school_class_id'    => ['required_if:target_type,class', 'nullable', 'exists:school_classes,id'],
            'target_role'        => ['required_if:target_type,role', 'nullable', 'in:admin,guru,siswa'],
            'target_user_ids'    => ['required_if:target_type,specific', 'nullable', 'array'],
            'target_user_ids.*'  => ['exists:users,id'],
            'publish_at'         => ['nullable', 'date'],
            'expires_at'         => ['nullable', 'date', 'after:publish_at'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'   => 'Judul pengumuman wajib diisi.',
            'content.required' => 'Isi pengumuman wajib diisi.',
        ];
    }
}
