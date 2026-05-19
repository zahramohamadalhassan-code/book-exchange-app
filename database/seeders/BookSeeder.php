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

        if ($students->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $books = [
            ['title' => 'مبادئ الفيزياء العامة', 'author' => 'د. محمد العلي', 'price' => 25.50],
            ['title' => 'أساسيات الرياضيات الهندسية', 'author' => 'د. أحمد الحسن', 'price' => 30.00],
            ['title' => 'مقدمة في البرمجة بلغة C++', 'author' => 'د. فاطمة الزهراء', 'price' => 40.00],
            ['title' => 'ميكانيكا المواد', 'author' => 'د. خالد المحمود', 'price' => 45.00],
            ['title' => 'الدوائر الكهربائية', 'author' => 'د. سارة الأحمد', 'price' => 35.50],
            ['title' => 'هندسة البرمجيات', 'author' => 'Ian Sommerville', 'price' => 60.00],
            ['title' => 'قواعد البيانات - المفاهيم والتطبيقات', 'author' => 'Ramez Elmasri', 'price' => 50.00],
            ['title' => 'نظرية الاتصالات', 'author' => 'Simon Haykin', 'price' => 55.00],
            ['title' => 'التحليل العددي', 'author' => 'Richard L. Burden', 'price' => 42.00],
            ['title' => 'الإلكترونيات التناظرية', 'author' => 'Adel S. Sedra', 'price' => 65.00],
            ['title' => 'الذكاء الاصطناعي - مقدمة شاملة', 'author' => 'Stuart Russell', 'price' => 70.00],
            ['title' => 'شبكات الحاسوب', 'author' => 'Andrew Tanenbaum', 'price' => 55.00],
            ['title' => 'أنظمة التشغيل', 'author' => 'Silberschatz', 'price' => 60.00],
            ['title' => 'معالجة الإشارات الرقمية', 'author' => 'John G. Proakis', 'price' => 48.00],
            ['title' => 'الرياضيات المتقطعة', 'author' => 'Kenneth H. Rosen', 'price' => 38.00],
        ];

        $conditions = ['excellent', 'good', 'fair', 'poor'];
        $offerTypes = ['sale', 'exchange', 'donate'];

        foreach ($books as $index => $bookData) {
            $offerType = $offerTypes[array_rand($offerTypes)];
            Book::create([
                'user_id' => $students->random()->id,
                'category_id' => $categories->random()->id,
                'title' => $bookData['title'],
                'author' => $bookData['author'],
                'cover_image_url' => null,
                'condition' => $conditions[array_rand($conditions)],
                'offer_type' => $offerType,
                'price' => $offerType === 'donate' ? null : $bookData['price'],
                'status' => 'available',
                'moderation_status' => 'approved',
            ]);
        }
    }
}
