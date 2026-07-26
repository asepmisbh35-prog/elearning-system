<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users');
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa'])->default('alfa');
            $table->dateTime('checked_in_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('document_path')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->dateTime('approved_at')->nullable();
            $table->json('status_log')->nullable();
            $table->timestamps();

            $table->unique(['attendance_session_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
