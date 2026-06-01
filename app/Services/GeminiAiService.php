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
    public function extractBookDetails(string $imageUrl, string $categoriesList = ''): array
    {
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            return $this->fallbackBookDetails();
        }

        try {
            $imageData = base64_encode(file_get_contents($imageUrl));
            $mimeType = $this->getMimeType($imageUrl);

            $categoriesSection = '';
            if (!empty($categoriesList)) {
                $categoriesSection = "\n\nقائمة الأقسام الموجودة بالمنصة:\n{$categoriesList}\nاختر القسم الأنسب من القائمة أعلاه. إذا لم تجد تطابقاً اكتب الاسم كما يظهر على الغلاف.\n";
            }

            $prompt = 'حلل هذه الصورة بدقة. قد تكون صورة غلاف كتاب أو غلاف محاضرة جامعية.

المهام:
1. تحقق أن الصورة مقبولة: يجب أن تكون لغلاف كتاب أو محاضرة جامعية فقط. ارفض أسئلة الامتحانات وأوراق الاختبارات وكشوف الدرجات والصور الشخصية وأي شيء غير تعليمي.
2. استخرج البيانات:
   - title: عنوان الكتاب أو المحاضرة الظاهر على الغلاف
   - author: اسم المؤلف أو المحاضر إن وُجد
   - condition: حالة الكتاب المادي من الصورة (excellent/good/fair/poor)
   - study_year: السنة الدراسية إن وُجدت (مثال: السنة الأولى، السنة الثانية...)
   - department_name: القسم والاختصاص الدقيق. إذا مكتوب "كلية الهندسة" فقط بدون فرع، استنتج الفرع من عنوان الكتاب:
     * برمجة أو حواسيب أو شبكات → هندسة الحواسيب
     * اتصالات أو إشارات أو هوائيات → هندسة الاتصالات
     * كهرباء أو إلكترونيات أو دوائر كهربائية → هندسة كهربائية
     * ميكانيكا أو آلات أو تصنيع → هندسة ميكانيكية
     * بناء أو تصميم معماري أو إنشائي → هندسة مدنية
     * إدارة أو محاسبة أو تمويل → إدارة أعمال / محاسبة
     * طب أو تشريح أو صيدلة → طب بشري / صيدلة
     * حقوق أو قانون → قانون
     * رياضيات أو فيزياء أو كيمياء → علوم أساسية
     * لغة عربية أو إنجليزية أو ترجمة → آداب / لغات
   - faculty_name: اسم الكلية (مثال: كلية الهندسة، كلية الطب)
   - university_name: اسم الجامعة إن وُجد' . $categoriesSection . '
   - category_id: رقم القسم الأنسب من القائمة المرفقة (رقم صحيح)، وإذا لم تجد تطابقاً ضعه null
أجب JSON فقط:
{"accepted": true, "title": "...", "author": "...", "condition": "...", "study_year": "...", "department_name": "...", "faculty_name": "...", "university_name": "...", "category_id": null, "reason": ""}

غير مقبول:
{"accepted": false, "title": "", "author": "", "condition": "", "study_year": "", "department_name": "", "faculty_name": "", "university_name": "", "category_id": null, "reason": "سبب الرفض"}';

            $response = $this->sendRequest([
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt
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

                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning('AI Service: JSON parse error in extractBookDetails', ['raw' => $text]);
                    return $this->fallbackBookDetails();
                }

                $accepted = $data['accepted'] ?? true;
                if (!$accepted) {
                    return [
                        'title'           => '',
                        'author'          => '',
                        'condition'       => '',
                        'study_year'      => '',
                        'department_name' => '',
                        'faculty_name'    => '',
                        'university_name' => '',
                        'rejected'        => true,
                        'reject_reason'   => $data['reason'] ?? 'الصورة غير مقبولة',
                    ];
                }

                $validConditions = ['excellent', 'good', 'fair', 'poor'];
                $condition = $data['condition'] ?? '';
                if (!in_array($condition, $validConditions)) {
                    $condition = '';
                }

                return [
                    'title'           => $data['title'] ?? '',
                    'author'          => $data['author'] ?? '',
                    'condition'       => $condition,
                    'study_year'      => $data['study_year'] ?? '',
                    'department_name' => $data['department_name'] ?? '',
                    'faculty_name'    => $data['faculty_name'] ?? '',
                    'university_name' => $data['university_name'] ?? '',
                    'category_id'     => $data['category_id'] ?? null,
                ];
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
     *   excellent = 1.25 ل.س/صفحة
     *   good      = 1.0 ل.س/صفحة
     *   fair      = 0.75 ل.س/صفحة
     *   poor      = 0.5 ل.س/صفحة
     * السعر = عدد الصفحات × سعر الصفحة حسب الحالة
     */
    public function predictPrice(string $title, string $condition, string $author = '', int $pagesCount = 0): float
    {
        $pricePerPage = [
            'excellent' => 1.25,
            'good'      => 1.0,
            'fair'      => 0.75,
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
            $pricingInfo = "سعر الصفحة حسب الحالة: ممتاز=1.25 ل.س، جيد=1 ل.س، مقبول=0.75 ل.س، ضعيف=0.5 ل.س.";
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
                            'text' => 'حلل هذه الصورة وأجب بتنسيق JSON فقط: {"safe": true/false, "is_book": true/false, "reason": "سبب الرفض إن وُجد"}. القواعد الصارمة: 1) هل تحتوي على محتوى غير لائق أو عنيف أو غير مناسب لمنصة تعليمية؟ 2) هل الصورة هي لغلاف كتاب أو محاضرة جامعية فقط؟ الصور المقبولة: غلاف كتاب، صفحات كتاب، غلاف محاضرة. الصور المرفوضة: أسئلة امتحانات، أوراق امتحانية، كشوف درجات، صور شخصية، طبيعة، طعام، سيارات، وأي شيء ليس غلاف كتاب أو محاضرة. إذا كانت الصورة لأسئلة امتحان أو اختبار، ارفضها مع السبب.'
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
                    return ['safe' => false, 'reason' => 'الصورة ليست لغلاف كتاب أو محاضرة'];
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
            $pages = $pdf->getPages();
            $pageCount = count($pages);

            $cleanText = preg_replace('/\bCamScanner\b/i', '', $text);
            $cleanText = trim(preg_replace('/\s+/', ' ', $cleanText));

            if (empty($cleanText) || mb_strlen($cleanText) < 20) {
                if ($pageCount > 0) {
                    return ['safe' => true, 'reason' => ''];
                }
                return ['safe' => false, 'reason' => 'لم يتم استخراج نص من الملف.'];
            }

            $textSample = mb_substr($cleanText, 0, 4000);

            $response = $this->sendRequest([
                [
                    'role' => 'system',
                    'content' => 'أنت مراقب محتوى في منصة تعليمية جامعية.'
                ],
                [
                    'role' => 'user',
                    'content' => "إليك نص مستخرج من ملف PDF رفعه طالب على منصة جامعية:\n\n---\n{$textSample}\n---\n\nحلل هذا النص وأجب بتنسيق JSON فقط: {\"is_study_material\": true/false, \"reason\": \"سبب الرفض\"}\n\nشروط القبول - يجب أن يحتوي على واحد على الأقل:\n- ملخص لمادة دراسية أو محاضرة أكاديمية\n- كتاب أو فصل من كتاب جامعي\n- ملاحظات دراسية أو حل مسائل\n- بحث علمي أو ورقة أكاديمية\n- شرح لمفاهيم أكاديمية\n- تعريفات أو مصطلحات أكاديمية\n\nارفض الملف إذا كان يحتوي على:\n- محتوى غير لائق أو عنيف\n- روايات أو قصص غير دراسية\n- إعلانات أو تسويق\n- أي شيء لا علاقة له بالدراسة الأكاديمية"
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
                    return ['safe' => false, 'reason' => $reason ?: 'الملف لا يحتوي على محتوى دراسي'];
                }

                return ['safe' => true, 'reason' => ''];
            }

            return ['safe' => true, 'reason' => ''];

        } catch (\Exception $e) {
            Log::error('AI Service Error (moderatePdf): ' . $e->getMessage());
            return ['safe' => true, 'reason' => ''];
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

    /**
     * تحليل محتوى ملف PDF لاستخراج وصف المحتوى
     * لا يتم تخزين الملف، فقط تحليله واستخراج الوصف
     */
    public function analyzePdfContent(string $pdfPath): array
    {
        if (empty($this->apiKey) || empty($this->apiUrl)) {
            return ['description' => ''];
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();

            $cleanText = preg_replace('/\bCamScanner\b/i', '', $text);
            $cleanText = trim(preg_replace('/\s+/', ' ', $cleanText));

            if (empty($cleanText) || mb_strlen($cleanText) < 20) {
                return ['description' => ''];
            }

            $textSample = mb_substr($cleanText, 0, 6000);

            $response = $this->sendRequest([
                [
                    'role' => 'user',
                    'content' => "أنت خبير في تلخيص المحتوى الأكاديمي. إليك نص مستخرج من كتاب أو محاضرة جامعية:\n\n---\n{$textSample}\n---\n\nقم بتحليل هذا المحتوى وأجب بتنسيق JSON فقط: {\"description\": \"وصف مختصر للمحتوى يتضمن العناوين الرئيسية والمواضيع التي يغطيها بشكل عام، بحد أقصى 300 حرف\"}"
                ]
            ]);

            if ($response && isset($response['choices'][0]['message']['content'])) {
                $text = $response['choices'][0]['message']['content'];
                $text = preg_replace('/```json\s*/', '', $text);
                $text = preg_replace('/```\s*/', '', $text);
                $text = trim($text);

                $data = json_decode($text, true);
                if (isset($data['description'])) {
                    return ['description' => $data['description']];
                }
            }

            return ['description' => ''];

        } catch (\Exception $e) {
            Log::error('AI Service Error (analyzePdfContent): ' . $e->getMessage());
            return ['description' => ''];
        }
    }

    private function fallbackBookDetails(): array
    {
        return ['title' => '', 'author' => '', 'condition' => '', 'study_year' => '', 'department_name' => '', 'faculty_name' => '', 'university_name' => ''];
    }

    private function fallbackPrice(string $condition, int $pagesCount = 0): float
    {
        $pricePerPage = [
            'excellent' => 1.25,
            'good'      => 1.0,
            'fair'      => 0.75,
            'poor'      => 0.5,
        ];

        $perPage = $pricePerPage[$condition] ?? 1.0;

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
