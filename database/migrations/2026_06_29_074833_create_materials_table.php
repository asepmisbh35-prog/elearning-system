<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meeting_id')->constrained('meetings')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('order')->default(1);
            $table->unsignedSmallInteger('estimated_minutes')->default(5); // 1-300
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('sequential_unlock')->default(false);
            $table->enum('unlock_method', ['scroll', 'manual'])->default('scroll'); // scroll=80%, manual=tombol
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
