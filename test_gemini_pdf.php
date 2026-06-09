<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pdfPath = 'T/استكشاف المعرفة د.زيد قريطم.pdf';
$pdfData = base64_encode(file_get_contents($pdfPath));

$payload = [
    'model' => config('ai.model', 'gemini-1.5-flash'), // assuming gemini-1.5-flash or gemini-3-flash
    'messages' => [
        [
            'role' => 'user',
            'content' => [
                [
                    'type' => 'text',
                    'text' => 'حلل هذا الملف وأجب بتنسيق JSON فقط: {"description": "وصف مختصر للمحتوى يتضمن العناوين الرئيسية والمواضيع التي يغطيها بشكل عام، بحد أقصى 300 حرف"}'
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
$response = \Illuminate\Support\Facades\Http::timeout(60)
    ->withHeaders([
        'Content-Type' => 'application/json',
        'x-litellm-api-key' => config('ai.api_key', ''),
    ])
    ->post(config('ai.api_url', ''), $payload);

echo "Status: " . $response->status() . "\n";
echo "Body: " . substr($response->body(), 0, 500) . "\n";
