<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition(): array
    {
        $book = Book::inRandomOrder()->first();
        $requester = User::where('id', '!=', $book?->user_id)->inRandomOrder()->first();

        return [
            'book_id' => $book?->id ?? 1,
            'requester_id' => $requester?->id ?? 2,
            'owner_id' => $book?->user_id ?? 1,
            'meeting_date' => fake()->optional(0.5)->dateTimeBetween('now', '+30 days'),
            'meeting_time' => fake()->optional(0.5)->time('H:i'),
            'meeting_location' => fake()->optional(0.5)->randomElement([
                'بوابة الجامعة الرئيسية',
                'المكتبة المركزية',
                'كافتيريا كلية الهندسة',
                'ساحة كلية العلوم',
                'مدخل المختبرات',
            ]),
            'status' => fake()->randomElement(['pending', 'accepted', 'completed', 'cancelled']),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'completed',
            'meeting_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'meeting_time' => fake()->time('H:i'),
            'meeting_location' => 'بوابة الجامعة الرئيسية',
        ]);
    }
}
