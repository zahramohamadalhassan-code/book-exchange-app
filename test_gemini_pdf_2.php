<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pdfPath = 'T/استكشاف المعرفة د.زيد قريطم.pdf';
$pdfData = base64_encode(file_get_contents($pdfPath));

$payload = [
    'model' => config('ai.model', 'gemini-1.5-flash'), 
    'messages' => [
        [
            'role' => 'user',
            'content' => [
                [
                    'type' => 'text',
                    'text' => 'أنت خبير أكاديمي. يرجى تحليل هذا الملف (الذي قد يحتوي على نصوص أو صور ممسوحة ضوئياً) واستخراج وصف دقيق وعام لمحتواه يتضمن المواضيع الأساسية المذكورة فيه بحد أقصى 300 حرف. أجب بتنسيق JSON فقط: {"description": "..."}'
                ],
                [
                    'type' => 'image_url',
                    'image_url' => [
                        'url' => "data:application/pdf;base64,{$pdfData}"
                    ]
                ]
            ]
        ]
    ],
    'max_tokens' => 1000,
];

echo "Sending...\n";
$start = microtime(true);
$response = \Illuminate\Support\Facades\Http::timeout(300)
    ->withHeaders([
        'Content-Type' => 'application/json',
        'x-litellm-api-key' => config('ai.api_key', ''),
    ])
    ->post(config('ai.api_url', ''), $payload);

$duration = microtime(true) - $start;
echo "Time taken: " . round($duration, 2) . "s\n";
echo "Status: " . $response->status() . "\n";
echo "Body: " . substr($response->body(), 0, 500) . "\n";
