<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::where('name', 'Student')->first();

        User::factory(20)->create([
            'role_id' => $studentRole->id,
        ]);
    }
}
