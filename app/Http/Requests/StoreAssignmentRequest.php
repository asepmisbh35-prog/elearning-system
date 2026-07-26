<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi guru dicek di controller
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'instructions'        => ['nullable', 'string'],
            'due_date'            => ['required', 'date'],
            'max_score'           => ['required', 'integer', 'min:1', 'max:1000'],
            'grade_component_id'  => ['nullable', 'exists:grade_components,id'],
            'status'              => ['required', 'in:draft,published'],
            'target_type'         => ['required', 'in:all,specific'],
            'target_student_ids'  => ['required_if:target_type,specific', 'array'],
            'target_student_ids.*' => ['exists:students,id'],
            'files.*'             => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,jpg,jpeg,png,zip'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Judul tugas wajib diisi.',
            'due_date.required' => 'Batas waktu wajib diisi.',
            'max_score.required' => 'Nilai maksimal wajib diisi.',
            'target_student_ids.required_if' => 'Pilih minimal satu siswa untuk tugas spesifik.',
            'files.*.max'       => 'Ukuran tiap file maksimal 20MB.',
            'files.*.mimes'     => 'Format file harus PDF, Word, JPG, PNG, atau ZIP.',
        ];
    }

    /**
     * Validasi tambahan: total ukuran semua file gabungan maks 20MB, maks 5 file.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $files = $this->file('files') ?? [];

            if (count($files) > 5) {
                $validator->errors()->add('files', 'Maksimal 5 file lampiran.');
            }

            $totalSize = collect($files)->sum(fn($f) => $f->getSize());
            if ($totalSize > 20 * 1024 * 1024) {
                $validator->errors()->add('files', 'Total ukuran semua lampiran maksimal 20MB.');
            }
        });
    }
}
