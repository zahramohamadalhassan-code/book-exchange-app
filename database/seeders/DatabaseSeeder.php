<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\DigitalNote;
use App\Models\Transaction;
use App\Models\Rating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. إنشاء الأدوار (Admin, Student)
        $this->call(RoleSeeder::class);
        $this->command->info('✅ Roles seeded.');

        // 2. إنشاء حساب المدير
        $this->call(AdminUserSeeder::class);
        $this->command->info('✅ Admin user seeded.');

        // 3. إنشاء التصنيفات (كليات وأقسام)
        $this->call(CategorySeeder::class);
        $this->command->info('✅ Categories seeded.');

        // 4. إنشاء طلاب (بيانات حقيقية)
        $this->call(StudentSeeder::class);
        $this->command->info('✅ Students seeded.');

        // 5. إنشاء كتب
        $this->call(BookSeeder::class);
        $this->command->info('✅ Books seeded.');

        // 6. إنشاء ملخصات رقمية
        $this->call(DigitalNoteSeeder::class);
        $this->command->info('✅ Digital Notes seeded.');

        // 7. إنشاء عمليات تبادل
        $this->call(TransactionSeeder::class);
        $this->command->info('✅ Transactions seeded.');

        // 8. إنشاء تقييمات
        $this->call(RatingSeeder::class);
        $this->command->info('✅ Ratings seeded.');
    }
}
