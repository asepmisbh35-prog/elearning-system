<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->longText('content'); // rich text
            $table->string('image_path')->nullable();
            $table->enum('target_type', ['all', 'class', 'role', 'specific']);
            $table->foreignId('school_class_id')->nullable()->constrained('school_classes')->nullOnDelete();
            $table->string('target_role')->nullable(); // admin|guru|siswa, dipakai kalau target_type=role
            $table->json('target_user_ids')->nullable(); // dipakai kalau target_type=specific
            $table->dateTime('publish_at')->nullable(); // null = langsung tampil
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
