<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::where('moderation_status', 'approved')->get();
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'Student'))->get();

        if ($books->isEmpty() || $students->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'accepted', 'completed', 'cancelled'];
        
        // إنشاء معاملات متنوعة
        foreach ($books->take(15) as $index => $book) {
            $requester = $students->where('id', '!=', $book->user_id)->random();
            
            // نجعل بعض المعاملات مكتملة لتكون قابلة للتقييم لاحقاً
            $status = $index < 5 ? 'completed' : $statuses[array_rand($statuses)];
            
            $transaction = Transaction::create([
                'book_id' => $book->id,
                'requester_id' => $requester->id,
                'owner_id' => $book->user_id,
                'status' => $status,
                'meeting_date' => now()->addDays(rand(1, 5))->toDateString(),
                'meeting_time' => now()->addHours(rand(1, 10))->toTimeString(),
                'meeting_location' => 'مكتبة الكلية',
            ]);

            // تحديث حالة الكتاب في حال كانت المعاملة مكتملة
            if ($status === 'completed') {
                $book->update(['status' => 'sold']);
            }
        }
    }
}
