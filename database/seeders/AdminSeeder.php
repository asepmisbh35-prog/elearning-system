<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'name'      => 'Administrator',
                'password'  => Hash::make('password123'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        Admin::firstOrCreate(
            ['user_id' => $user->id],
            ['phone' => null]
        );
    }
}
