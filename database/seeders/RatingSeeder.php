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

        foreach ($completedTransactions as $transaction) {
            Rating::factory()->create([
                'transaction_id' => $transaction->id,
                'reviewer_id' => $transaction->requester_id,
                'reviewed_user_id' => $transaction->owner_id,
            ]);
        }
    }
}
