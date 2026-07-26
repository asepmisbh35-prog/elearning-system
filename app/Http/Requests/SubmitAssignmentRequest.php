<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi (siswa & visibility tugas) dicek di controller
    }

    public function rules(): array
    {
        return [
            'text_answer' => ['nullable', 'string'],
            'files.*'     => ['nullable', 'file', 'max:20480', 'mimes:pdf,doc,docx,jpg,jpeg,png,zip'],
        ];
    }

    public function messages(): array
    {
        return [
            'files.*.max'   => 'Ukuran tiap file maksimal 20MB.',
            'files.*.mimes' => 'Format file harus PDF, Word, JPG, PNG, atau ZIP.',
        ];
    }

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

            // Minimal harus isi salah satu: teks atau file
            $hasText = filled($this->text_answer);
            $hasFiles = count($files) > 0;
            $hasExistingFiles = $this->boolean('keep_existing_files');

            if (! $hasText && ! $hasFiles && ! $hasExistingFiles) {
                $validator->errors()->add('text_answer', 'Isi jawaban teks atau unggah minimal satu file.');
            }
        });
    }
}
