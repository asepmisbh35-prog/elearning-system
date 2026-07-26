<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')
                ->constrained('school_classes')
                ->cascadeOnDelete();
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            // Cegah siswa masuk kelas yang sama 2x
            $table->unique(['school_class_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
