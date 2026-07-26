<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->foreignId('grade_component_id')->nullable()->constrained('grade_components')->nullOnDelete();
            $table->string('title');
            $table->longText('instructions')->nullable(); // rich text
            $table->dateTime('due_date');
            $table->unsignedSmallInteger('max_score')->default(100);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->enum('target_type', ['all', 'specific'])->default('all');
            $table->json('target_student_ids')->nullable(); // dipakai kalau target_type = specific
            $table->json('attachments')->nullable(); // [{path, original_name, size}]
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
