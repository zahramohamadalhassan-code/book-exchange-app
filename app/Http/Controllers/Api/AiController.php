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

        $moderation = $aiService->moderateImage($fullPath);

        if (!$moderation['safe']) {
            @unlink($fullPath);
            return response()->json([
                'success' => false,
                'message' => $moderation['reason'],
            ], 422);
        }

        $details = $aiService->extractBookDetails($fullPath);

        @unlink($fullPath);

        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    /**
     * اقتراح سعر الكتاب عبر AI (بالليرة السورية)
     */
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

    /**
     * فحص ملف PDF عبر AI للتحقق من أنه ملخص/كتاب دراسي
     */
    public function moderatePdf(Request $request, GeminiAiService $aiService)
    {
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:10000',
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
}
