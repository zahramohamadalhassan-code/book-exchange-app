<?php
require 'vendor/autoload.php';

use setasign\Fpdi\Fpdi;

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $pdfPath = 'T/استكشاف المعرفة د.زيد قريطم.pdf';

    $pdf = new Fpdi();
    $pageCount = $pdf->setSourceFile($pdfPath);
    
    // We only want the first 3 pages (or fewer if the document is shorter)
    $pagesToExtract = min(3, $pageCount);
    
    for ($pageNo = 1; $pageNo <= $pagesToExtract; $pageNo++) {
        $templateId = $pdf->importPage($pageNo);
        $size = $pdf->getTemplateSize($templateId);
        
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($templateId);
    }
    
    // Output the newly created PDF to a string
    $tinyPdfContent = $pdf->Output('S');
    $pdfData = base64_encode($tinyPdfContent);
    
    echo "Extracted $pagesToExtract pages. New PDF size: " . strlen($tinyPdfContent) . " bytes\n";
    
    // Send to Gemini
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

    echo "Sending to AI...\n";
    $start = microtime(true);
    $response = \Illuminate\Support\Facades\Http::timeout(60)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'x-litellm-api-key' => config('ai.api_key', ''),
        ])
        ->post(config('ai.api_url', ''), $payload);

    $duration = microtime(true) - $start;
    echo "Time taken: " . round($duration, 2) . "s\n";
    echo "Status: " . $response->status() . "\n";
    echo "Body: " . substr($response->body(), 0, 500) . "\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
