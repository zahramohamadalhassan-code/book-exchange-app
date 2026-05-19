<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DigitalNote;
use App\Models\User;
use Illuminate\Database\Seeder;

class DigitalNoteSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::whereHas('role', fn ($q) => $q->where('name', 'Student'))->get();
        $categories = Category::all();

        if ($students->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $notes = [
            ['title' => 'ملخص شامل لمقرر الفيزياء 1', 'file_url' => 'https://example.com/physics1.pdf', 'pages' => 45],
            ['title' => 'حلول أسئلة دورات الرياضيات', 'file_url' => 'https://example.com/math_exams.pdf', 'pages' => 30],
            ['title' => 'ملاحظات هامة في برمجة C++', 'file_url' => 'https://example.com/cpp_notes.pdf', 'pages' => 25],
            ['title' => 'مشروع تخرج - نظام إدارة مكتبة', 'file_url' => 'https://example.com/project.pdf', 'pages' => 80],
            ['title' => 'ملخص مقرر الدوائر الكهربائية', 'file_url' => 'https://example.com/circuits.pdf', 'pages' => 50],
            ['title' => 'أساسيات هندسة البرمجيات', 'file_url' => 'https://example.com/se_basics.pdf', 'pages' => 40],
            ['title' => 'ملخص مقرر قواعد البيانات', 'file_url' => 'https://example.com/db_summary.pdf', 'pages' => 35],
            ['title' => 'مذكرات في الذكاء الاصطناعي', 'file_url' => 'https://example.com/ai_notes.pdf', 'pages' => 60],
            ['title' => 'تجارب مخبرية في الكيمياء', 'file_url' => 'https://example.com/chemistry_lab.pdf', 'pages' => 20],
            ['title' => 'أسئلة محلولة في الخوارزميات', 'file_url' => 'https://example.com/algorithms.pdf', 'pages' => 55],
        ];

        foreach ($notes as $noteData) {
            DigitalNote::create([
                'user_id' => $students->random()->id,
                'category_id' => $categories->random()->id,
                'title' => $noteData['title'],
                'pdf_file_url' => $noteData['file_url'],
                'description' => 'هذا الملف يحتوي على ' . $noteData['title'] . ' وهو مفيد جداً للتحضير للامتحانات.',
                'moderation_status' => 'approved',
            ]);
        }
    }
}
