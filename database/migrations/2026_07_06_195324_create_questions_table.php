<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->string('subject'); // bank soal per guru per mapel
            $table->string('category')->nullable(); // topik/kompetensi dasar
            $table->enum('type', [
                'multiple_choice',
                'true_false',
                'short_answer',
                'drag_drop',
                'word_search',
                'crossword',
                'fill_blank',
                'sorting',
                'timer_challenge',
                'true_false_swipe',
            ]);
            $table->longText('question_text');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('points')->default(1);
            $table->json('answer_keywords')->nullable(); // isian singkat: kata kunci jawaban
            $table->json('meta')->nullable(); // data fleksibel per tipe (grid word search, kalimat rumpang, dll)
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
