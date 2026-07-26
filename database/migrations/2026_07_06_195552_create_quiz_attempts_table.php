<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quizzes')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->unsignedSmallInteger('score')->nullable();
            $table->enum('status', ['in_progress', 'submitted', 'auto_submitted'])->default('in_progress');
            $table->unsignedTinyInteger('tab_switch_count')->default(0); // anti-kecurangan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
