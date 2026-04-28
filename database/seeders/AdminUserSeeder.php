<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();

        User::create([
            'role_id' => $adminRole->id,
            'university_id' => 'ADMIN-001',
            'full_name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'phone_number' => '0900000000',
            'is_banned' => false,
        ]);
    }
}
