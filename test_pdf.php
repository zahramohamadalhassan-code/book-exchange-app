<?php
require 'vendor/autoload.php';
$parser = new \Smalot\PdfParser\Parser();
try {
    $pdf = $parser->parseFile('T/استكشاف المعرفة د.زيد قريطم.pdf');
    $text = $pdf->getText();
    echo 'LENGTH: ' . mb_strlen($text) . PHP_EOL;
    echo 'SAMPLE: ' . mb_substr($text, 0, 200) . PHP_EOL;
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
