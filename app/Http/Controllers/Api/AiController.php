<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GeminiAiService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    /**
     * استخراج بيانات الكتاب من صورة الغلاف عبر AI
     * يُستدعى عبر AJAX من نموذج إضافة كتاب
     */
    public function extractBookDetails(Request $request, GeminiAiService $aiService)
    {
        $request->validate([
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $path = $request->file('cover_image')->store('temp/covers', 'public');
        $fullPath = storage_path('app/public/' . $path);

        $details = $aiService->extractBookDetails($fullPath);

        // حذف الملف المؤقت
        @unlink($fullPath);

        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    /**
     * اقتراح سعر الكتاب عبر AI
     */
    public function predictPrice(Request $request, GeminiAiService $aiService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'condition' => 'required|in:excellent,good,fair,poor',
        ]);

        $price = $aiService->predictPrice($request->title, $request->condition);

        return response()->json([
            'success' => true,
            'price' => $price,
        ]);
    }
}
