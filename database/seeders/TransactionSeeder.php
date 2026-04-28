<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $books = Book::where('moderation_status', 'approved')->get();
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'Student'))->get();

        foreach ($books->take(20) as $book) {
            $requester = $students->where('id', '!=', $book->user_id)->random();

            Transaction::factory()->create([
                'book_id' => $book->id,
                'requester_id' => $requester->id,
                'owner_id' => $book->user_id,
            ]);
        }
    }
}
