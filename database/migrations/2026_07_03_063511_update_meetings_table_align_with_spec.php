<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->renameColumn('title', 'topic');
            $table->unsignedInteger('order')->default(1)->after('school_class_id');
        });

        // Ganti enum status: draft/active/done → draft/published
        // (dilakukan terpisah karena rename+enum di MySQL kurang stabil dalam 1 statement)
        Schema::table('meetings', function (Blueprint $table) {
            $table->string('status')->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('meetings', function (Blueprint $table) {
            $table->renameColumn('topic', 'title');
            $table->dropColumn('order');
            $table->enum('status', ['draft', 'active', 'done'])->default('draft')->change();
        });
    }
};
