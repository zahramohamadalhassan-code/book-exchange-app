<?php

namespace Database\Factories;

use App\Models\DigitalNote;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DigitalNote>
 */
class DigitalNoteFactory extends Factory
{
    protected $model = DigitalNote::class;

    private array $noteTitles = [
        'ملخص الفيزياء - الفصل الأول',
        'ملخص الرياضيات التفاضلية',
        'مراجعة شاملة - البرمجة',
        'ملاحظات محاضرات الدوائر الكهربائية',
        'ملخص مادة الإلكترونيات',
        'أسئلة سنوات سابقة - قواعد البيانات',
        'ملخص شبكات الحاسوب',
        'مراجعة نهائية - أنظمة التشغيل',
        'حلول تمارين التحليل العددي',
        'ملخص الإحصاء والاحتمالات',
    ];

    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'category_id' => Category::inRandomOrder()->first()?->id ?? 1,
            'title' => fake()->randomElement($this->noteTitles),
            'description' => fake()->paragraph(2),
            'pdf_file_url' => 'notes/pdfs/sample_' . fake()->unique()->numerify('###') . '.pdf',
            'moderation_status' => fake()->randomElement(['pending', 'approved', 'approved', 'approved']),
        ];
    }
}
