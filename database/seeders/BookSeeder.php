<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'Student'))->get();
        $categories = Category::all();

        for ($i = 0; $i < 50; $i++) {
            Book::factory()->create([
                'user_id' => $students->random()->id,
                'category_id' => $categories->random()->id,
            ]);
        }
    }
}
