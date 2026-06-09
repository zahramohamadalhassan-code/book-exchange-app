<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function extractBookDetails(Request $request, GeminiAiService $aiService)
    {
        $request->validate([
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $path = $request->file('cover_image')->store('temp/covers', 'public');
        $fullPath = storage_path('app/public/' . $path);

        $existingCategories = Category::all();
        $categoriesList = $existingCategories->map(function ($c) {
            return "{$c->id}: {$c->faculty_name} - {$c->department_name} ({$c->study_year})";
        })->implode("\n");

        $details = $aiService->extractBookDetails($fullPath, $categoriesList);

        @unlink($fullPath);

        if (isset($details['rejected']) && $details['rejected']) {
            return response()->json([
                'success' => false,
                'message' => $details['reject_reason'] ?? 'الصورة غير مقبولة',
            ], 422);
        }

        if (isset($details['category_id']) && $details['category_id']) {
            $categoryId = $details['category_id'];
        } else {
            $categoryId = $this->findOrCreateCategory(
                $details['department_name'] ?? '',
                $details['study_year'] ?? '',
                $details['faculty_name'] ?? '',
                $details['university_name'] ?? '',
                $existingCategories
            );
        }

        if ($categoryId) {
            $details['category_id'] = $categoryId;
        }

        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    public function predictPrice(Request $request, GeminiAiService $aiService)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'condition'   => 'required|in:excellent,good,fair,poor',
            'author'      => 'nullable|string|max:255',
            'pages_count' => 'nullable|integer|min:1',
        ]);

        $price = $aiService->predictPrice(
            $request->title,
            $request->condition,
            $request->input('author', ''),
            $request->input('pages_count', 0)
        );

        return response()->json([
            'success' => true,
            'price'   => $price,
        ]);
    }

    public function moderatePdf(Request $request, GeminiAiService $aiService)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:25000',
        ]);

        $path = $request->file('pdf_file')->store('temp/pdfs', 'public');
        $fullPath = storage_path('app/public/' . $path);

        $moderation = $aiService->moderatePdf($fullPath);

        @unlink($fullPath);

        if (!$moderation['safe']) {
            return response()->json([
                'success' => false,
                'message' => $moderation['reason'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => '',
        ]);
    }

    public function analyzePdfContent(Request $request, GeminiAiService $aiService)
    {
        set_time_limit(180);

        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:25000',
        ]);

        $path = $request->file('pdf_file')->store('temp/pdfs', 'public');
        $fullPath = storage_path('app/public/' . $path);
        $originalName = $request->file('pdf_file')->getClientOriginalName();

        $result = $aiService->analyzePdfContent($fullPath, $originalName);

        @unlink($fullPath);

        if (empty($result['description'])) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم استخراج وصف من الملف',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'description' => $result['description'],
        ]);
    }

    private function findOrCreateCategory(string $departmentName, string $studyYear, string $facultyName, string $universityName, $existingCategories = null): ?int
    {
        if (empty($departmentName) && empty($studyYear) && empty($facultyName)) {
            return null;
        }

        $categories = $existingCategories ?? Category::all();

        foreach ($categories as $category) {
            $matches = 0;

            if (!empty($departmentName) && !empty($category->department_name)) {
                if (mb_strpos($category->department_name, $departmentName) !== false || mb_strpos($departmentName, $category->department_name) !== false || $category->department_name === $departmentName) {
                    $matches++;
                }
            }

            if (!empty($studyYear) && !empty($category->study_year)) {
                if (mb_strpos($category->study_year, $studyYear) !== false || mb_strpos($studyYear, $category->study_year) !== false || $category->study_year === $studyYear) {
                    $matches++;
                }
            }

            if (!empty($facultyName) && !empty($category->faculty_name)) {
                if (mb_strpos($category->faculty_name, $facultyName) !== false || mb_strpos($facultyName, $category->faculty_name) !== false || $category->faculty_name === $facultyName) {
                    $matches++;
                }
            }

            if ($matches >= 2) {
                return $category->id;
            }
        }

        $category = Category::create([
            'university_name' => $universityName ?: 'الجامعة الوطنية الخاصة',
            'faculty_name'    => $facultyName ?: 'غير محدد',
            'department_name' => $departmentName ?: 'غير محدد',
            'study_year'      => $studyYear ?: 'غير محدد',
        ]);

        return $category->id;
    }
}