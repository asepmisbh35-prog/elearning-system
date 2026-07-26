<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content'   => ['required_without:files', 'nullable', 'string', 'max:2000'],
            'files'     => ['nullable', 'array', 'max:3'],
            'files.*'   => ['file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $totalSize = collect($this->file('files') ?? [])->sum(fn($f) => $f->getSize());
            if ($totalSize > 10 * 1024 * 1024) {
                $validator->errors()->add('files', 'Total ukuran lampiran maksimal 10MB.');
            }
        });
    }
}
