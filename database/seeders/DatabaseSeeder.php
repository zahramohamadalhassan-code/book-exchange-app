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

        // 4. إنشاء 20 طالب وهمي
        User::factory(20)->create();
        $this->command->info('✅ 20 Students seeded.');

        // 5. إنشاء 50 كتاب وهمي
        Book::factory(50)->create();
        $this->command->info('✅ 50 Books seeded.');

        // 6. إنشاء 20 ملخص رقمي وهمي
        DigitalNote::factory(20)->create();
        $this->command->info('✅ 20 Digital Notes seeded.');

        // 7. إنشاء 15 عملية تبادل وهمية
        Transaction::factory(15)->create();
        $this->command->info('✅ 15 Transactions seeded.');

        // 8. إنشاء عمليات مكتملة ثم تقييمات
        $completedTransactions = Transaction::factory(5)->completed()->create();
        foreach ($completedTransactions as $transaction) {
            Rating::factory()->create([
                'transaction_id' => $transaction->id,
                'reviewer_id' => $transaction->requester_id,
                'reviewed_user_id' => $transaction->owner_id,
            ]);
        }
        $this->command->info('✅ 5 Completed transactions with ratings seeded.');
    }
}
