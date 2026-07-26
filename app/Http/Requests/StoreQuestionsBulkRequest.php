<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionsBulkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject'    => ['required', 'string', 'max:100'],
            'category'   => ['nullable', 'string', 'max:100'],
            'batch_name' => ['required', 'string', 'max:150'],

            'questions'                       => ['required', 'array', 'min:1'],
            'questions.*.type' => ['required', 'in:multiple_choice,true_false,short_answer,true_false_swipe,sorting,fill_blank,timer_challenge,drag_drop,word_search,crossword'],
            'questions.*.question_text'        => ['required', 'string'],
            'questions.*.points'               => ['required', 'integer', 'min:1', 'max:100'],
            'questions.*.options'              => ['required_if:questions.*.type,multiple_choice,timer_challenge', 'array'],
            'questions.*.options.*'            => ['nullable', 'string'],
            'questions.*.correct_option'       => ['required_if:questions.*.type,multiple_choice,timer_challenge', 'integer'],
            'questions.*.true_false_answer'    => ['required_if:questions.*.type,true_false,true_false_swipe', 'in:true,false'],
            'questions.*.answer_keywords'      => ['nullable', 'string'],
            'questions.*.sorting_items'       => ['required_if:questions.*.type,sorting', 'array', 'min:2'],
            'questions.*.sorting_items.*'     => ['nullable', 'string'],
            'questions.*.blank_keywords'      => ['required_if:questions.*.type,fill_blank', 'array'],
            'questions.*.blank_keywords.*'    => ['nullable', 'string'],
            'questions.*.time_limit_seconds'   => ['required_if:questions.*.type,timer_challenge', 'nullable', 'integer', 'min:5', 'max:300'],
            'questions.*.bonus_max_percent'    => ['nullable', 'integer', 'min:0', 'max:100'],
            'questions.*.pairs_left'          => ['required_if:questions.*.type,drag_drop', 'array', 'min:2'],
            'questions.*.pairs_left.*'        => ['nullable', 'string'],
            'questions.*.pairs_right'         => ['required_if:questions.*.type,drag_drop', 'array', 'min:2'],
            'questions.*.pairs_right.*'       => ['nullable', 'string'],
            'questions.*.crossword_words'   => ['required_if:questions.*.type,crossword', 'array', 'min:2'],
            'questions.*.crossword_words.*'  => ['nullable', 'string'],
            'questions.*.crossword_clues'   => ['required_if:questions.*.type,crossword', 'array', 'min:2'],
            'questions.*.crossword_clues.*'  => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required'                    => 'Mata pelajaran wajib diisi.',
            'batch_name.required'                 => 'Nama paket soal wajib diisi, contoh: "UAS IPA Kelas 9".',   // ← baris baru ditambahkan di sini
            'questions.required'                  => 'Minimal harus ada 1 soal.',
            'questions.*.question_text.required'  => 'Ada soal yang teks-nya masih kosong.',
            'questions.*.options.required_if'     => 'Ada soal pilihan ganda yang belum diisi pilihannya.',
            'questions.*.correct_option.required_if' => 'Ada soal pilihan ganda yang belum dipilih jawaban benarnya.',
            'questions.*.true_false_answer.required_if' => 'Ada soal benar/salah yang belum dipilih jawabannya.',
            'questions.*.sorting_items.required_if' => 'Minimal 2 item urutan wajib diisi.',
            'questions.*.word_search_words'   => ['required_if:questions.*.type,word_search', 'string'],
            
        ];
    }
}
