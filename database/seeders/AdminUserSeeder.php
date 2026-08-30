<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@garml.id'],
            [
                'user_id' => (string) Str::uuid(),
                'role' => 'admin',
                'nama' => 'Admin GARML',
                'email' => 'admin@garml.id',
                'password_hash' => Hash::make('admin123'),
                'level' => 1,
                'total_xp' => 0,
                'created_at' => now(),
            ]
        );
    }
}
