<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_component_id')->constrained('grade_components')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->decimal('score', 5, 2); // nilai akhir komponen ini (0-100)
            $table->boolean('is_manual')->default(false); // true = input manual guru (praktik dll), false = hasil rekap otomatis
            $table->text('note')->nullable(); // catatan deskriptif
            $table->foreignId('input_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['grade_component_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
