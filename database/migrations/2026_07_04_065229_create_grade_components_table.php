<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_class_id')->constrained('school_classes')->cascadeOnDelete();
            $table->string('name'); // contoh: "Tugas Harian", "UTS", "UAS"
            $table->unsignedTinyInteger('weight')->default(0); // bobot dalam %
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_components');
    }
};
