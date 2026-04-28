<?php

namespace Database\Factories;

use App\Models\Rating;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rating>
 */
class RatingFactory extends Factory
{
    protected $model = Rating::class;

    private array $comments = [
        'تعامل ممتاز، شكراً جزيلاً!',
        'الكتاب بحالة جيدة كما وصف.',
        'التسليم كان في الموعد المحدد.',
        'شخص موثوق، أنصح بالتعامل معه.',
        'الكتاب لم يكن بالحالة المتوقعة.',
        'تأخر قليلاً لكن بشكل عام جيد.',
        'ممتاز! سأتعامل معه مجدداً.',
        'شكراً على التبرع بالكتاب.',
        null,
        null,
    ];

    public function definition(): array
    {
        $transaction = Transaction::where('status', 'completed')
            ->whereDoesntHave('rating')
            ->inRandomOrder()
            ->first();

        return [
            'transaction_id' => $transaction?->id ?? 1,
            'reviewer_id' => $transaction?->requester_id ?? 1,
            'reviewed_user_id' => $transaction?->owner_id ?? 2,
            'stars' => fake()->numberBetween(1, 5),
            'comment' => fake()->randomElement($this->comments),
        ];
    }
}
