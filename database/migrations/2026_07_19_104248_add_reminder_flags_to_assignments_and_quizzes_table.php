<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->boolean('reminder_h1_sent')->default(false);
            $table->boolean('reminder_h3jam_sent')->default(false);
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->boolean('reminder_1jam_sent')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['reminder_h1_sent', 'reminder_h3jam_sent']);
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('reminder_1jam_sent');
        });
    }
};
