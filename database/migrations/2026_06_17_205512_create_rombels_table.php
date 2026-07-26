<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rombels', function (Blueprint $table) {
            $table->id();
            $table->string('name');           // contoh: 10A, 10B, 11A
            $table->string('grade');          // jenjang: 10, 11, 12 / VII, VIII, IX
            $table->string('academic_year'); // contoh: 2024/2025
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rombels');
    }
};
