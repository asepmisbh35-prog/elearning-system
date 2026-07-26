<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['student_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_sessions');
    }
};
