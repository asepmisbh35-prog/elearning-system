<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->enum('type', ['text', 'video', 'pdf_viewer', 'pdf_flipbook', 'link']);
            $table->unsignedSmallInteger('order')->default(1);

            // Tipe: text
            $table->longText('content')->nullable(); // HTML dari rich text editor

            // Tipe: video
            $table->string('video_url')->nullable();     // URL YouTube/Vimeo asli
            $table->string('video_embed_id')->nullable(); // ID yang diekstrak
            $table->enum('video_platform', ['youtube', 'vimeo', 'other'])->nullable();

            // Tipe: pdf_viewer & pdf_flipbook
            $table->string('file_path')->nullable();       // path file PDF
            $table->string('original_filename')->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->unsignedSmallInteger('page_count')->nullable(); // untuk flipbook
            $table->enum('flipbook_status', ['pending', 'processing', 'ready', 'failed'])->nullable();
            $table->string('flipbook_path')->nullable(); // folder webp hasil konversi

            // Tipe: link
            $table->string('link_url')->nullable();
            $table->string('link_title')->nullable();
            $table->string('link_domain')->nullable(); // diekstrak dari URL

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_blocks');
    }
};
