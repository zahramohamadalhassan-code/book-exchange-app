<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // كلية الهندسة المعلوماتية
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة المعلوماتية', 'department_name' => 'هندسة البرمجيات', 'study_year' => 'السنة الأولى'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة المعلوماتية', 'department_name' => 'هندسة البرمجيات', 'study_year' => 'السنة الثانية'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة المعلوماتية', 'department_name' => 'الذكاء الاصطناعي', 'study_year' => 'السنة الأولى'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة المعلوماتية', 'department_name' => 'الذكاء الاصطناعي', 'study_year' => 'السنة الثانية'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة المعلوماتية', 'department_name' => 'الشبكات والأمن السيبراني', 'study_year' => 'السنة الأولى'],

            // كلية الهندسة الكهربائية
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة الكهربائية', 'department_name' => 'هندسة الاتصالات', 'study_year' => 'السنة الأولى'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة الكهربائية', 'department_name' => 'هندسة الاتصالات', 'study_year' => 'السنة الثانية'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة الكهربائية', 'department_name' => 'هندسة الإلكترونيات', 'study_year' => 'السنة الأولى'],

            // كلية الهندسة المدنية
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة المدنية', 'department_name' => 'الهندسة الإنشائية', 'study_year' => 'السنة الأولى'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة المدنية', 'department_name' => 'هندسة المساحة', 'study_year' => 'السنة الأولى'],

            // كلية الهندسة الميكانيكية
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة الميكانيكية', 'department_name' => 'هندسة الإنتاج', 'study_year' => 'السنة الأولى'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية الهندسة الميكانيكية', 'department_name' => 'هندسة الطاقة', 'study_year' => 'السنة الثانية'],

            // كلية العلوم
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية العلوم', 'department_name' => 'الرياضيات', 'study_year' => 'السنة الأولى'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية العلوم', 'department_name' => 'الفيزياء', 'study_year' => 'السنة الأولى'],
            ['university_name' => 'الجامعة التقنية', 'faculty_name' => 'كلية العلوم', 'department_name' => 'الكيمياء', 'study_year' => 'السنة الثانية'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
