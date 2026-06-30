<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Smalot\PdfParser\Parser;

if ($argc < 2) {
    echo "Usage: php debug-pdf-parser.php <path-to-pdf>\n";
    echo "Example: php debug-pdf-parser.php storage/app/temp/xxx.pdf\n";
    exit(1);
}

$pdfPath = $argv[1];

if (!file_exists($pdfPath)) {
    echo "File not found: $pdfPath\n";
    exit(1);
}

try {
    $parser = new Parser();
    echo "Reading PDF: $pdfPath\n\n";
    
    $pdf = $parser->parseFile($pdfPath);
    $text = $pdf->getText();
    
    echo "=== EXTRACTED TEXT (first 1000 chars) ===\n";
    echo substr($text, 0, 1000);
    echo "\n=== END ===\n\n";
    
    echo "Text length: " . strlen($text) . " characters\n";
    echo "\nNow testing parser service...\n\n";
    
    $service = app(\App\Services\CertificateParserService::class);
    $result = $service->parse($pdfPath);
    
    echo "=== PARSER RESULT ===\n";
    print_r($result);
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
