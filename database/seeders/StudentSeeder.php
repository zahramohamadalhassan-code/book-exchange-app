<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::where('name', 'Student')->first();
        $roleId = $studentRole ? $studentRole->id : 2;

        $students = [
            [
                'university_id' => '202010258',
                'full_name' => 'أحمد المحمد',
                'email' => 'ahmad.202010258@university.edu',
                'phone_number' => '0933123456',
            ],
            [
                'university_id' => '202010259',
                'full_name' => 'سارة علي',
                'email' => 'sara.202010259@university.edu',
                'phone_number' => '0944123456',
            ],
            [
                'university_id' => '202010260',
                'full_name' => 'محمود سعيد',
                'email' => 'mahmoud.202010260@university.edu',
                'phone_number' => '0955123456',
            ],
            [
                'university_id' => '202010261',
                'full_name' => 'ليلى خالد',
                'email' => 'layla.202010261@university.edu',
                'phone_number' => '0966123456',
            ],
            [
                'university_id' => '202010262',
                'full_name' => 'عمر حسن',
                'email' => 'omar.202010262@university.edu',
                'phone_number' => '0988123456',
            ],
            [
                'university_id' => '202010263',
                'full_name' => 'نور عبد الله',
                'email' => 'nour.202010263@university.edu',
                'phone_number' => '0999123456',
            ],
            [
                'university_id' => '202010264',
                'full_name' => 'طارق زياد',
                'email' => 'tarek.202010264@university.edu',
                'phone_number' => '0933987654',
            ],
            [
                'university_id' => '202010265',
                'full_name' => 'ريم سالم',
                'email' => 'reem.202010265@university.edu',
                'phone_number' => '0944987654',
            ],
            [
                'university_id' => '202010266',
                'full_name' => 'يوسف إبراهيم',
                'email' => 'yousef.202010266@university.edu',
                'phone_number' => '0955987654',
            ],
            [
                'university_id' => '202010267',
                'full_name' => 'فاطمة محمود',
                'email' => 'fatima.202010267@university.edu',
                'phone_number' => '0966987654',
            ],
        ];

        foreach ($students as $student) {
            User::create([
                'role_id' => $roleId,
                'university_id' => $student['university_id'],
                'full_name' => $student['full_name'],
                'email' => $student['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'phone_number' => $student['phone_number'],
                'is_banned' => false,
            ]);
        }
    }
}
