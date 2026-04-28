<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'university_name' => fake()->company().' University',
            'faculty_name' => fake()->randomElement(['Engineering', 'Science', 'Arts', 'Business', 'Medicine']),
            'department_name' => fake()->randomElement(['Computer Science', 'Electrical', 'Mechanical', 'Civil', 'Chemical']),
            'study_year' => fake()->randomElement(['First', 'Second', 'Third', 'Fourth', 'Fifth']),
        ];
    }
}
