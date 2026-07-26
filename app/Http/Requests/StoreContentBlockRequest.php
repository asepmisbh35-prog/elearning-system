<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContentBlockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi guru dicek di controller (authorizeTeacher)
    }

    public function rules(): array
    {
        return [
            'type'  => ['required', 'in:text,video,pdf_viewer,pdf_flipbook,link'],
            'order' => ['required', 'integer', 'min:1'],

            // ── Text ──
            'content' => ['required_if:type,text', 'nullable', 'string'],

            // ── Video ──
            'video_url' => ['required_if:type,video', 'nullable', 'url'],

            // ── PDF Viewer ──
            // (file divalidasi umum di bawah, tapi khusus wajib untuk pdf_viewer)
            // ── PDF Flipbook ──
            'file' => [
                'required_if:type,pdf_viewer,pdf_flipbook',
                'nullable',
                'file',
                'mimes:pdf',
                'max:20480', // 20MB
            ],

            // ── Link ──
            'link_url'   => ['required_if:type,link', 'nullable', 'url'],
            'link_title' => ['required_if:type,link', 'nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'             => 'Tipe blok konten wajib dipilih.',
            'content.required_if'       => 'Isi teks wajib diisi.',
            'video_url.required_if'     => 'URL video wajib diisi.',
            'video_url.url'             => 'URL video tidak valid.',
            'file.required_if'          => 'File PDF wajib diupload.',
            'file.mimes'                => 'File harus berformat PDF.',
            'file.max'                  => 'Ukuran file PDF maksimal 20MB.',
            'link_url.required_if'      => 'URL link wajib diisi.',
            'link_title.required_if'    => 'Judul link wajib diisi.',
        ];
    }
}
