<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->nullable(); // null untuk tipe yang tidak butuh (misal sorting pakai 'order')
            $table->unsignedInteger('order')->default(0);
            $table->json('meta')->nullable(); // misal: pasangan drag&drop {pair_id: x}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
    }
};
