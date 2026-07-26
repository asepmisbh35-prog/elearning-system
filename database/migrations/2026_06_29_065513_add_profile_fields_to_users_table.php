<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('users', 'phone')) $cols[] = 'phone';
            if (Schema::hasColumn('users', 'photo')) $cols[] = 'photo';
            if ($cols) $table->dropColumn($cols);
        });
    }
};
