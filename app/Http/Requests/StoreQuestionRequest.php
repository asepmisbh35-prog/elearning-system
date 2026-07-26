<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject'         => ['required', 'string', 'max:100'],
            'category'        => ['nullable', 'string', 'max:100'],
            'type' => ['required', 'in:multiple_choice,true_false,short_answer,true_false_swipe,sorting,fill_blank,timer_challenge,drag_drop,word_search,crossword'],
            'question_text'   => ['required', 'string'],
            'image'           => ['nullable', 'image', 'max:5120'],
            'points'          => ['required', 'integer', 'min:1', 'max:100'],
            'time_limit_seconds' => 'required_if:type,timer_challenge|nullable|integer|min:5|max:300',
            'bonus_max_percent'  => 'nullable|integer|min:0|max:100',

            // Pilihan Ganda & Timer Challenge (sama-sama pakai opsi PG)
            'options'                  => ['required_if:type,multiple_choice,timer_challenge', 'array', 'min:2', 'max:5'],
            'options.*'                => ['required_if:type,multiple_choice,timer_challenge', 'string'],
            'correct_option'           => ['required_if:type,multiple_choice,timer_challenge', 'integer'],

            // Benar/Salah
            'true_false_answer'        => ['required_if:type,true_false,true_false_swipe', 'in:true,false'],

            // Isian Singkat
            'answer_keywords'          => ['nullable', 'string'],

            // Sorting
            'sorting_items'            => ['required_if:type,sorting', 'array', 'min:2'],
            'sorting_items.*'          => ['required_if:type,sorting', 'string'],

            // Fill in the Blank
            'blank_keywords'         => ['required_if:type,fill_blank', 'array', 'min:1'],
            'blank_keywords.*'       => ['nullable', 'string'],

            // Drag & Drop
            'pairs_left'          => ['required_if:type,drag_drop', 'array', 'min:2'],
            'pairs_left.*'         => ['required_if:type,drag_drop', 'string'],
            'pairs_right'          => ['required_if:type,drag_drop', 'array', 'min:2'],
            'pairs_right.*'        => ['required_if:type,drag_drop', 'string'],

            // Word Search
            'word_search_words'   => ['required_if:type,word_search', 'string'],

            // Crossword
            'crossword_words'   => ['required_if:type,crossword', 'array', 'min:2'],
            'crossword_words.*'  => ['nullable', 'string'],
            'crossword_clues'   => ['required_if:type,crossword', 'array', 'min:2'],
            'crossword_clues.*'  => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'question_text.required' => 'Teks soal wajib diisi.',
            'options.required_if'    => 'Minimal 2 pilihan jawaban wajib diisi.',
            'correct_option.required_if' => 'Pilih salah satu jawaban yang benar.',
            'true_false_answer.required_if' => 'Pilih jawaban benar atau salah.',
            'sorting_items.required_if' => 'Minimal 2 item urutan wajib diisi.',
            'word_search_words.required_if' => 'Minimal isi 1 kata untuk Word Search.',
        ];
    }
}
