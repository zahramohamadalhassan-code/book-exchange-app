<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role_id' => Role::where('name', 'Student')->first()?->id ?? 2,
            'university_id' => 'STU-' . fake()->unique()->numerify('#####'),
            'full_name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'phone_number' => fake()->numerify('09########'),
            'is_banned' => false,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * حالة المستخدم المحظور
     */
    public function banned(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_banned' => true,
        ]);
    }

    /**
     * حالة المدير
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role_id' => Role::where('name', 'Admin')->first()?->id ?? 1,
        ]);
    }
}
