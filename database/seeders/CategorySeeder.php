<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['faculty' => 'كلية الهندسة', 'department' => 'معلوماتية'],
            ['faculty' => 'كلية الهندسة', 'department' => 'اتصالات'],
            ['faculty' => 'كلية الهندسة', 'department' => 'مدنية'],
            ['faculty' => 'كلية الهندسة', 'department' => 'معمارية'],
            ['faculty' => 'كلية العلوم الإدارية والمالية', 'department' => 'علوم إدارية'],
            ['faculty' => 'كلية طب الأسنان', 'department' => 'طب أسنان'],
            ['faculty' => 'كلية الصيدلة', 'department' => 'صيدلة'],
        ];

        $studyYears = ['السنة الأولى', 'السنة الثانية', 'السنة الثالثة', 'السنة الرابعة', 'السنة الخامسة'];

        foreach ($categories as $cat) {
            foreach ($studyYears as $year) {
                // استثناء السنة الخامسة لكلية العلوم الإدارية والمالية
                if ($cat['faculty'] === 'كلية العلوم الإدارية والمالية' && $year === 'السنة الخامسة') {
                    continue;
                }

                Category::create([
                    'university_name' => 'الجامعة الوطنية الخاصة',
                    'faculty_name' => $cat['faculty'],
                    'department_name' => $cat['department'],
                    'study_year' => $year,
                ]);
            }
        }
    }
}
