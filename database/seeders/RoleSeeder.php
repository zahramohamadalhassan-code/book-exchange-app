<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'Admin', 'description' => 'مدير النظام']);
        Role::create(['name' => 'Student', 'description' => 'طالب جامعي']);
    }
}
