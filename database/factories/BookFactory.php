<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    private array $bookTitles = [
        'مبادئ الفيزياء العامة',
        'أساسيات الرياضيات الهندسية',
        'مقدمة في البرمجة بلغة C++',
        'ميكانيكا المواد',
        'الدوائر الكهربائية',
        'هندسة البرمجيات',
        'قواعد البيانات - المفاهيم والتطبيقات',
        'نظرية الاتصالات',
        'التحليل العددي',
        'الإلكترونيات التناظرية',
        'الذكاء الاصطناعي - مقدمة شاملة',
        'شبكات الحاسوب',
        'أنظمة التشغيل',
        'معالجة الإشارات الرقمية',
        'الرياضيات المتقطعة',
        'الكيمياء العامة',
        'مبادئ الاقتصاد الهندسي',
        'ديناميكا حرارية',
        'مقاومة المواد',
        'الإحصاء والاحتمالات',
    ];

    private array $authors = [
        'د. محمد العلي',
        'د. أحمد الحسن',
        'د. فاطمة الزهراء',
        'د. خالد المحمود',
        'د. سارة الأحمد',
        'Serway & Jewett',
        'Thomas Calculus',
        'Deitel & Deitel',
        'Andrew Tanenbaum',
        'Silberschatz',
    ];

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? 1,
            'title' => fake()->randomElement($this->bookTitles),
            'author' => fake()->randomElement($this->authors),
            'cover_image_url' => null,
            'condition' => fake()->randomElement(['excellent', 'good', 'fair', 'poor']),
            'offer_type' => fake()->randomElement(['sale', 'exchange', 'donate']),
            'price' => function (array $attributes) {
                return $attributes['offer_type'] === 'donate' ? null : fake()->randomFloat(2, 5, 100);
            },
            'status' => 'available',
            'moderation_status' => fake()->randomElement(['pending', 'approved', 'approved', 'approved']),
        ];
    }
}
