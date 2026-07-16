<?php

declare(strict_types=1);

namespace App\Services\Upload\Parsers;

use Illuminate\Support\Facades\Log;

final class PdfParserService
{
    public function extractText(string $filePath): string
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            Log::warning('Smalot PDF Parser not installed');
            return '';
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Exception $e) {
            Log::error('PDF parsing failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    public function isScanned(string $filePath): bool
    {
        $text = $this->extractText($filePath);
        return empty(trim($text));
    }

    public function getPageCount(string $filePath): int
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            return 0;
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            return count($pdf->getPages());
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function extractMetadata(string $filePath): array
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            return [];
        }

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $details = $pdf->getDetails();

            return [
                'title' => $details['Title'] ?? null,
                'author' => $details['Author'] ?? null,
                'subject' => $details['Subject'] ?? null,
                'creator' => $details['Creator'] ?? null,
                'producer' => $details['Producer'] ?? null,
                'creation_date' => $details['CreationDate'] ?? null,
                'mod_date' => $details['ModDate'] ?? null,
            ];
        } catch (\Exception $e) {
            return [];
        }
    }
}
