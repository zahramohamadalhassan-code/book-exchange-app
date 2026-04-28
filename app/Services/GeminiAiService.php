<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * خدمة الذكاء الاصطناعي - Google Gemini API
 * تستخدم لاستخراج بيانات الكتب، اقتراح الأسعار، ومراقبة المحتوى
 */
class GeminiAiService
{
    private string $apiKey;
    private string $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->apiUrl = config('services.gemini.api_url',
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent'
        );
    }

    /**
     * استخراج تفاصيل الكتاب من صورة الغلاف (Smart Auto-Fill)
     */
    public function extractBookDetails(string $imageUrl): array
    {
        if (empty($this->apiKey)) {
            return $this->fallbackBookDetails();
        }

        try {
            $imageData = base64_encode(file_get_contents($imageUrl));
            $mimeType = $this->getMimeType($imageUrl);

            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->apiUrl}?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'انظر إلى صورة غلاف هذا الكتاب واستخرج العنوان والمؤلف. أجب بتنسيق JSON فقط: {"title": "عنوان الكتاب", "author": "اسم المؤلف"}'],
                                ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageData]]
                            ]
                        ]
                    ],
                    'generationConfig' => ['responseMimeType' => 'application/json']
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');
                $data = json_decode($text, true);
                if (isset($data['title'])) {
                    return ['title' => $data['title'] ?? '', 'author' => $data['author'] ?? ''];
                }
            }

            Log::warning('Gemini API: Failed to extract book details', ['status' => $response->status()]);
            return $this->fallbackBookDetails();

        } catch (\Exception $e) {
            Log::error('Gemini API Error (extractBookDetails): ' . $e->getMessage());
            return $this->fallbackBookDetails();
        }
    }

    /**
     * اقتراح سعر عادل للكتاب بناءً على عنوانه وحالته
     */
    public function predictPrice(string $title, string $condition): float
    {
        if (empty($this->apiKey)) {
            return $this->fallbackPrice($condition);
        }

        try {
            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->apiUrl}?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => "أنت خبير في تسعير الكتب الجامعية المستعملة. كتاب بعنوان \"{$title}\" بحالة \"{$condition}\". اقترح سعر بيع عادل بالدولار. أجب برقم فقط بتنسيق JSON: {\"price\": 25.00}"]
                            ]
                        ]
                    ],
                    'generationConfig' => ['responseMimeType' => 'application/json']
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');
                $data = json_decode($text, true);
                if (isset($data['price']) && is_numeric($data['price'])) {
                    return round((float) $data['price'], 2);
                }
            }

            return $this->fallbackPrice($condition);

        } catch (\Exception $e) {
            Log::error('Gemini API Error (predictPrice): ' . $e->getMessage());
            return $this->fallbackPrice($condition);
        }
    }

    /**
     * فحص الصورة للتحقق من عدم وجود محتوى غير لائق
     */
    public function moderateImage(string $imageUrl): bool
    {
        if (empty($this->apiKey)) {
            return true;
        }

        try {
            $imageData = base64_encode(file_get_contents($imageUrl));
            $mimeType = $this->getMimeType($imageUrl);

            $response = Http::timeout(30)
                ->retry(3, 1000)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->apiUrl}?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => 'هل تحتوي هذه الصورة على محتوى غير لائق أو عنيف أو غير مناسب لمنصة تعليمية جامعية؟ أجب بتنسيق JSON: {"safe": true} أو {"safe": false}'],
                                ['inline_data' => ['mime_type' => $mimeType, 'data' => $imageData]]
                            ]
                        ]
                    ],
                    'generationConfig' => ['responseMimeType' => 'application/json']
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text', '');
                $data = json_decode($text, true);
                return $data['safe'] ?? true;
            }

            return true;

        } catch (\Exception $e) {
            Log::error('Gemini API Error (moderateImage): ' . $e->getMessage());
            return true;
        }
    }

    private function fallbackBookDetails(): array
    {
        return ['title' => '', 'author' => ''];
    }

    private function fallbackPrice(string $condition): float
    {
        return match ($condition) {
            'excellent' => 35.00,
            'good' => 25.00,
            'fair' => 15.00,
            'poor' => 8.00,
            default => 20.00,
        };
    }

    private function getMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
    }
}
