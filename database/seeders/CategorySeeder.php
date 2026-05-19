<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'هندسة معلوماتية',
            'هندسة اتصالات',
            'هندسة مدنية',
            'هندسة معمارية والتخطيطي المعماري',
            'العلوم الادارية والمالية',
            'طب الاسنان',
            'الصيدلة',
        ];

        $studyYears = ['السنة الأولى', 'السنة الثانية', 'السنة الثالثة', 'السنة الرابعة', 'السنة الخامسة'];

        foreach ($departments as $department) {
            foreach ($studyYears as $year) {
                // استثناء السنة الخامسة لكلية العلوم الإدارية والمالية
                if ($department === 'العلوم الادارية والمالية' && $year === 'السنة الخامسة') {
                    continue;
                }

                // للتبسيط سنعتبر اسم الكلية والقسم هو نفسه بناءً على طلب المستخدم
                Category::create([
                    'university_name' => 'الجامعة',
                    'faculty_name' => $department,
                    'department_name' => 'عام',
                    'study_year' => $year,
                ]);
            }
        }
    }
}
