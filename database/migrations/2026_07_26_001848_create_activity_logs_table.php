<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // material_completed, quiz_completed, assignment_submitted, dll
            $table->string('subject')->nullable();
            $table->string('description');
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['student_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
