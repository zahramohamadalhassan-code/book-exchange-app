<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $completedTransactions = Transaction::where('status', 'completed')->get();

        if ($completedTransactions->isEmpty()) {
            return;
        }

        $commentMap = [
            5 => 'ممتاز',
            4 => 'جيد جداً',
            3 => 'جيد',
            2 => 'عادي',
            1 => 'سيء',
        ];

        foreach ($completedTransactions as $transaction) {
            $stars = rand(1, 5); // تقييمات من 1 إلى 5
            Rating::create([
                'transaction_id' => $transaction->id,
                'reviewer_id' => $transaction->requester_id,
                'reviewed_user_id' => $transaction->owner_id,
                'stars' => $stars,
                'comment' => $commentMap[$stars],
            ]);
        }
    }
}
