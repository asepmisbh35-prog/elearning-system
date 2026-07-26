<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->longText('text_answer')->nullable();
            $table->json('attachments')->nullable(); // [{path, original_name, size}]
            $table->enum('timing_status', ['on_time', 'late'])->nullable(); // null = belum submit
            $table->dateTime('submitted_at')->nullable();

            // Penilaian
            $table->unsignedSmallInteger('score')->nullable();
            $table->text('feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('graded_at')->nullable();

            // Revisi
            $table->unsignedTinyInteger('revision_count')->default(0); // maks 3
            $table->enum('revision_status', ['none', 'requested', 'revised'])->default('none');
            $table->dateTime('revision_deadline')->nullable();

            $table->timestamps();

            $table->unique(['assignment_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
