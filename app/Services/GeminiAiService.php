<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * خدمة الذكاء الاصطناعي - LiteLLM Proxy (OpenAI-compatible)
 * تستخدم لاستخراج بيانات الكتب، اقتراح الأسعار، ومراقبة المحتوى
 * المزود: api.abdalgani.com عبر LiteLLM → Google Gemini / NVIDIA / etc.
 */
class GeminiAiService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private int $maxTokens;
    private float $temperature;

    public function __construct()
    {
        $this->apiKey      = config('ai.api_key', '');
        $this->apiUrl      = config('ai.api_url', '');
        $this->model       = config('ai.model', 'gemini-3-flash-preview');
        $this->maxTokens   = (int) config('ai.max_tokens', 4096);
        $this->temperature = (float) config('ai.temperature', 0.7);
    }

    /**
     * استخراج تفاصيل الكتاب من صورة الغلاف (Smart Auto-Fill)
     * يشمل: العنوان، المؤلف، وتحديد حالة الكتاب من الصورة
     */
    public function extractBookDetails(string $imageUrl): array
    {
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            return $this->fallbackBookDetails();
        }

        try {
            $imageData = base64_encode(file_get_contents($imageUrl));
            $mimeType = $this->getMimeType($imageUrl);

            $response = $this->sendRequest([
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'انظر إلى صورة غلاف هذا الكتاب واستخرج العنوان والمؤلف وحالة الكتاب الفعلية. الحالات الممكنة: excellent (ممتاز/كالجديد)، good (جيد)، fair (مقبول/به علامات)، poor (ضعيف/مهترئ). أجب بتنسيق JSON فقط: {"title": "عنوان الكتاب", "author": "اسم المؤلف", "condition": "excellent"}'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$imageData}"
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                $text = $response['choices'][0]['message']['content'];
                $text = preg_replace('/```json\s*/', '', $text);
                $text = preg_replace('/```\s*/', '', $text);
                $text = trim($text);

                $data = json_decode($text, true);
                if (isset($data['title'])) {
                    $validConditions = ['excellent', 'good', 'fair', 'poor'];
                    $condition = $data['condition'] ?? '';
                    if (!in_array($condition, $validConditions)) {
                        $condition = '';
                    }
                    return [
                        'title'     => $data['title'] ?? '',
                        'author'    => $data['author'] ?? '',
                        'condition' => $condition,
                    ];
                }
            }

            Log::warning('AI Service: Failed to extract book details');
            return $this->fallbackBookDetails();

        } catch (\Exception $e) {
            Log::error('AI Service Error (extractBookDetails): ' . $e->getMessage());
            return $this->fallbackBookDetails();
        }
    }

    /**
     * اقتراح سعر عادل للكتاب بالليرة السورية (SYP)
     * سعر الصفحة حسب الحالة:
     *   excellent = 2.0 ل.س/صفحة
     *   good      = 1.5 ل.س/صفحة
     *   fair      = 1.0 ل.س/صفحة
     *   poor      = 0.5 ل.س/صفحة
     * السعر = عدد الصفحات × سعر الصفحة حسب الحالة
     */
    public function predictPrice(string $title, string $condition, string $author = '', int $pagesCount = 0): float
    {
        $pricePerPage = [
            'excellent' => 2.0,
            'good'      => 1.5,
            'fair'      => 1.0,
            'poor'      => 0.5,
        ];

        $conditionLabels = [
            'excellent' => 'ممتاز (كالجديد)',
            'good'      => 'جيد (مستعمل بحالة جيدة)',
            'fair'      => 'مقبول (به بعض العلامات)',
            'poor'      => 'ضعيف (مهترئ قليلاً)',
        ];

        $perPage = $pricePerPage[$condition] ?? 1.5;
        $conditionLabel = $conditionLabels[$condition] ?? 'جيد';
        $authorPart = $author ? " للمؤلف \"{$author}\"" : '';

        $calculatedPrice = $pagesCount > 0
            ? round($pagesCount * $perPage, 2)
            : null;

        if (empty($this->apiKey) || empty($this->apiUrl)) {
            return $this->fallbackPrice($condition, $pagesCount);
        }

        try {
            $pricingInfo = "سعر الصفحة حسب الحالة: ممتاز=2 ل.س، جيد=1.5 ل.س، مقبول=1 ل.س، ضعيف=0.5 ل.س.";
            $pagesInfo = $pagesCount > 0
                ? " الكتاب يحتوي على {$pagesCount} صفحة."
                : '';
            $priceGuidance = $calculatedPrice !== null
                ? " السعر المطلوب = {$pagesCount} صفحة × {$perPage} ل.س/صفحة = {$calculatedPrice} ليرة سورية. لا تبتعد عن هذا السعر."
                : " اقترح سعراً مناسباً بين 0 و 500 ليرة سورية.";

            $response = $this->sendRequest([
                [
                    'role' => 'user',
                    'content' => "أنت خبير في تسعير الكتب الجامعية المستعملة في سوريا. كتاب بعنوان \"{$title}\"{$authorPart} بحالة \"{$conditionLabel}\".{$pagesInfo} {$pricingInfo}{$priceGuidance} أجب بتنسيق JSON فقط: {\"price\": 150}"
                ]
            ]);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                $text = $response['choices'][0]['message']['content'];
                $text = preg_replace('/```json\s*/', '', $text);
                $text = preg_replace('/```\s*/', '', $text);
                $text = trim($text);

                $data = json_decode($text, true);
                if (isset($data['price']) && is_numeric($data['price'])) {
                    $price = (float) $data['price'];
                    if ($calculatedPrice !== null) {
                        $price = max(0, min($price, $calculatedPrice * 1.2));
                    } else {
                        $price = max(0, min($price, 500));
                    }
                    return round($price, 2);
                }
            }

            return $this->fallbackPrice($condition, $pagesCount);

        } catch (\Exception $e) {
            Log::error('AI Service Error (predictPrice): ' . $e->getMessage());
            return $this->fallbackPrice($condition, $pagesCount);
        }
    }

    /**
     * فحص الصورة: التحقق من عدم وجود محتوى غير لائق
     * والتحقق من أن الصورة تمت بصلة لغلاف كتاب أو محاضرة أو ملخص
     *
     * @return array ['safe' => bool, 'reason' => string]
     */
    public function moderateImage(string $imageUrl): array
    {
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            return ['safe' => true, 'reason' => ''];
        }

        try {
            $imageData = base64_encode(file_get_contents($imageUrl));
            $mimeType = $this->getMimeType($imageUrl);

            $response = $this->sendRequest([
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => 'حلل هذه الصورة وأجب بتنسيق JSON فقط: {"safe": true/false, "is_book": true/false, "reason": "سبب الرفض إن وُجد"}. القواعد: 1) هل تحتوي على محتوى غير لائق أو عنيف أو غير مناسب لمنصة تعليمية؟ 2) هل الصورة تمت بصلة لكتاب أو محاضرة أو ملخص جامعي (غلاف كتاب، صفحات كتاب، ملاحظات دراسية، ملخص)؟ إذا لم تكن الصورة لكتاب أو محاضرة أو ملخص (مثلاً: صورة شخصية، طبيعة، طعام، سيارة، إلخ) ارفضها.'
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => "data:{$mimeType};base64,{$imageData}"
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                $text = $response['choices'][0]['message']['content'];
                $text = preg_replace('/```json\s*/', '', $text);
                $text = preg_replace('/```\s*/', '', $text);
                $text = trim($text);

                $data = json_decode($text, true);
                $safe = $data['safe'] ?? true;
                $isBook = $data['is_book'] ?? true;
                $reason = $data['reason'] ?? '';

                if (!$safe) {
                    return ['safe' => false, 'reason' => $reason ?: 'محتوى غير لائق'];
                }
                if (!$isBook) {
                    return ['safe' => false, 'reason' => 'الصورة لا تمت بصلة لغلاف كتاب أو محاضرة أو ملخص'];
                }

                return ['safe' => true, 'reason' => ''];
            }

            return ['safe' => true, 'reason' => ''];

        } catch (\Exception $e) {
            Log::error('AI Service Error (moderateImage): ' . $e->getMessage());
            return ['safe' => true, 'reason' => ''];
        }
    }

    /**
     * فحص ملف PDF: التحقق من أنه ملخص/كتاب/محاضرة وليس محتوى غير مناسب
     *
     * @return array ['safe' => bool, 'reason' => string]
     */
    public function moderatePdf(string $pdfPath): array
    {
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            return ['safe' => true, 'reason' => ''];
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();

            if (empty(trim($text))) {
                return ['safe' => false, 'reason' => 'لم يتم استخراج نص من الملف. يرجى رفع ملف PDF يحتوي على نص قابل للقراءة.'];
            }

            $textSample = mb_substr($text, 0, 4000);

            $response = $this->sendRequest([
                [
                    'role' => 'system',
                    'content' => 'أنت مراقب محتوى صارم في منصة تعليمية جامعية. يجب أن ترفض أي ملف لا يحتوي على محتوى تعليمي أكاديمي واضح.'
                ],
                [
                    'role' => 'user',
                    'content' => "إليك نص مستخرج من ملف PDF رفعه طالب كملخص دراسي على منصة جامعية:\n\n---\n{$textSample}\n---\n\nحلل هذا النص بدقة وأجب بتنسيق JSON فقط: {\"is_study_material\": true/false, \"reason\": \"سبب الرفض\"}\n\nشروط القبول الصارمة - يجب أن يحتوي على واحد على الأقل:\n- ملخص لمادة دراسية أو محاضرة أكاديمية\n- كتاب أو فصل من كتاب جامعي\n- ملاحظات دراسية أو حل مسائل\n- بحث علمي أو ورقة أكاديمية\n- شرح لمفاهيم أكاديمية (رياضيات، فيزياء، برمجة، طب، هندسة...)\n- امتحانات أو أسئلة دراسية\n\nارفض الملف إذا كان يحتوي على:\n- نصوص غير مفهومة أو رموز عشوائية\n- محتوى غير لائق أو عنيف\n- روايات أو قصص غير دراسية\n- أغاني أو كلمات أغاني\n- إعلانات أو تسويق\n- وصفات طبخ أو رياضة أو ترفيه\n- محتوى شخصي أو رسائل خاصة\n- أي شيء لا علاقة له بالدراسة الأكاديمية\n\nكن صارماً: إذا لم تكن متأكداً بنسبة 80% أن المحتوى أكاديمي، ارفضه."
                ]
            ]);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                $content = $response['choices'][0]['message']['content'];
                $content = preg_replace('/```json\s*/', '', $content);
                $content = preg_replace('/```\s*/', '', $content);
                $content = trim($content);

                $data = json_decode($content, true);
                $isStudy = $data['is_study_material'] ?? false;
                $reason = $data['reason'] ?? '';

                if (!$isStudy) {
                    return ['safe' => false, 'reason' => $reason ?: 'الملف لا يحتوي على ملخص دراسي أو محاضرة أو كتاب جامعي'];
                }

                return ['safe' => true, 'reason' => ''];
            }

            return ['safe' => false, 'reason' => 'لم يتم التحقق من الملف، يرجى المحاولة مرة أخرى'];

        } catch (\Exception $e) {
            Log::error('AI Service Error (moderatePdf): ' . $e->getMessage());
            return ['safe' => false, 'reason' => 'حدث خطأ أثناء تحليل الملف، يرجى المحاولة مرة أخرى'];
        }
    }

    /**
     * إرسال طلب إلى LiteLLM Proxy (OpenAI-compatible format)
     *
     * @param array $messages مصفوفة الرسائل بتنسيق OpenAI
     * @return array|null الاستجابة المحللة أو null عند الفشل
     */
    private function sendRequest(array $messages): ?array
    {
        $payload = [
            'model'       => $this->model,
            'messages'    => $messages,
            'max_tokens'  => $this->maxTokens,
            'temperature' => $this->temperature,
        ];

        Log::debug('AI Service: Sending request to LiteLLM proxy', [
            'url'   => $this->apiUrl,
            'model' => $this->model,
        ]);

        $response = Http::timeout(60)
            ->retry(3, 2000)
            ->withHeaders([
                'Content-Type'       => 'application/json',
                'x-litellm-api-key'  => $this->apiKey,
            ])
            ->post($this->apiUrl, $payload);

        if ($response->successful()) {
            Log::debug('AI Service: Response received successfully');
            return $response->json();
        }

        Log::warning('AI Service: Request failed', [
            'status' => $response->status(),
            'body'   => $response->body(),
        ]);

        return null;
    }

    private function fallbackBookDetails(): array
    {
        return ['title' => '', 'author' => '', 'condition' => ''];
    }

    private function fallbackPrice(string $condition, int $pagesCount = 0): float
    {
        $pricePerPage = [
            'excellent' => 2.0,
            'good'      => 1.5,
            'fair'      => 1.0,
            'poor'      => 0.5,
        ];

        $perPage = $pricePerPage[$condition] ?? 1.5;

        if ($pagesCount > 0) {
            return round($pagesCount * $perPage, 2);
        }

        return match ($condition) {
            'excellent' => 250.00,
            'good'      => 175.00,
            'fair'      => 100.00,
            'poor'      => 50.00,
            default     => 150.00,
        };
    }

    private function getMimeType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'gif'         => 'image/gif',
            'webp'        => 'image/webp',
            default       => 'image/jpeg',
        };
    }
}
