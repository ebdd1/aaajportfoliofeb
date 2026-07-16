<?php

declare(strict_types=1);

namespace App\Services\Upload\Parsers;

use Illuminate\Support\Facades\Log;

final class DocxParserService
{
    public function extractText(string $filePath): string
    {
        if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
            Log::warning('PhpWord not installed');
            return '';
        }

        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= $this->extractTextFromElement($element) . "\n";
                }
            }

            return $text;
        } catch (\Exception $e) {
            Log::error('DOCX parsing failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    private function extractTextFromElement($element): string
    {
        $text = '';

        if (method_exists($element, 'getText')) {
            $text .= $element->getText();
        }

        if (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $child) {
                $text .= $this->extractTextFromElement($child);
            }
        }

        return $text;
    }
}
