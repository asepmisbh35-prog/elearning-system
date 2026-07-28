<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('rombel_id')->nullable()->after('user_id')->constrained('rombels')->nullOnDelete();
            $table->string('name')->nullable()->after('nisn');
            $table->boolean('has_registered')->default(false)->after('is_registered');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['rombel_id']);
            $table->dropColumn(['rombel_id', 'name', 'has_registered']);
        });
    }
};
