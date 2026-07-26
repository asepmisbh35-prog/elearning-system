<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'instructions'        => ['nullable', 'string'],
            'duration_minutes'    => ['required', 'integer', 'min:5', 'max:300'],
            'access_start_at'     => ['required', 'date'],
            'access_end_at'       => ['required', 'date', 'after:access_start_at'],
            'max_attempts'        => ['nullable', 'integer', 'min:1', 'max:20'],
            'score_method'        => ['required', 'in:best,last,average'],
            'display_mode'        => ['required', 'in:one_by_one,all_at_once'],
            'shuffle_questions'   => ['nullable', 'boolean'],
            'shuffle_options'     => ['nullable', 'boolean'],
            'show_score'          => ['required', 'in:immediately,held'],
            'show_review'         => ['nullable', 'boolean'],
            'grade_component_id'  => ['nullable', 'exists:grade_components,id'],
            'status'              => ['required', 'in:draft,published'],

            'question_ids'        => ['required', 'array', 'min:1'],
            'question_ids.*'      => ['exists:questions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'            => 'Judul kuis wajib diisi.',
            'duration_minutes.required' => 'Durasi kuis wajib diisi.',
            'access_start_at.required'  => 'Waktu mulai akses wajib diisi.',
            'access_end_at.after'       => 'Waktu selesai akses harus setelah waktu mulai.',
            'question_ids.required'     => 'Pilih minimal 1 soal dari bank soal.',
        ];
    }
}
