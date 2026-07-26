<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('grade_component_id')->nullable()->constrained('grade_components')->nullOnDelete();
            $table->string('title');
            $table->longText('instructions')->nullable();
            $table->unsignedSmallInteger('duration_minutes'); // 5-300
            $table->dateTime('access_start_at');
            $table->dateTime('access_end_at');
            $table->unsignedTinyInteger('max_attempts')->nullable(); // null = unlimited
            $table->enum('score_method', ['best', 'last', 'average'])->default('best');
            $table->enum('display_mode', ['one_by_one', 'all_at_once'])->default('one_by_one');
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->enum('show_score', ['immediately', 'held'])->default('immediately');
            $table->boolean('show_review')->default(false);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
