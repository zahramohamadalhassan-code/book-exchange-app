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

        $comments = [
            'التعامل ممتاز والكتاب بحالة جيدة جداً، شكراً لك.',
            'شخص محترم وسريع في الاستجابة.',
            'الكتاب كما في الوصف تماماً، تجربة رائعة.',
            'شكراً على الكتاب، أنصح بالتعامل معه.',
            'التعامل كان جيداً ولكن تأخر قليلاً في الرد.',
            'الكتاب فيه بعض الملاحظات ولكن السعر مناسب.',
        ];

        foreach ($completedTransactions as $transaction) {
            Rating::create([
                'transaction_id' => $transaction->id,
                'reviewer_id' => $transaction->requester_id,
                'reviewed_user_id' => $transaction->owner_id,
                'stars' => rand(3, 5), // تقييمات إيجابية إجمالاً بين 3 و 5
                'comment' => $comments[array_rand($comments)],
            ]);
        }
    }
}
