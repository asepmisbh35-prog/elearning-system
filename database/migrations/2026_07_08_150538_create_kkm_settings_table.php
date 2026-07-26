<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kkm_settings', function (Blueprint $table) {
            $table->id();
            // school_class_id NULL = KKM sekolah (dari Admin), diisi = override per kelas (dari Guru)
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->cascadeOnDelete();
            $table->unsignedTinyInteger('value'); // 0-100
            $table->foreignId('set_by')->constrained('users')->cascadeOnDelete();
            $table->dateTime('effective_from'); // berlaku mulai kapan (prospektif)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kkm_settings');
    }
};
