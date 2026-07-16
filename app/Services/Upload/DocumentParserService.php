<?php

declare(strict_types=1);

namespace App\Services\Upload;

use App\Services\Upload\Parsers\PdfParserService;
use App\Services\Upload\Parsers\DocxParserService;
use App\Services\Upload\Parsers\CvDataMapper;
use Illuminate\Support\Facades\Log;

final class DocumentParserService
{
    public function __construct(
        private readonly PdfParserService $pdfParser,
        private readonly DocxParserService $docxParser,
        private readonly CvDataMapper $cvDataMapper
    ) {}

    public function parse(string $filePath, string $mimeType): ParsedDocument
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        try {
            return match ($extension) {
                'pdf' => $this->parsePdf($filePath),
                'jpeg', 'jpg', 'png', 'webp' => $this->parseImage(),
                'docx' => $this->parseDocx($filePath),
                default => ParsedDocument::empty(),
            };
        } catch (\Exception $e) {
            Log::error('Document parsing failed', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);

            return ParsedDocument::empty()->withError($e->getMessage());
        }
    }

    private function parsePdf(string $filePath): ParsedDocument
    {
        $text = $this->pdfParser->extractText($filePath);

        if (empty(trim($text))) {
            Log::info('PDF appears to be scanned, no text extracted');
            $document = ParsedDocument::empty();
            $document->setNeedsOcr(true);
            return $document;
        }

        return $this->cvDataMapper->map($text);
    }

    private function parseImage(): ParsedDocument
    {
        $document = ParsedDocument::empty();
        $document->setNeedsOcr(true);
        return $document;
    }

    private function parseDocx(string $filePath): ParsedDocument
    {
        $text = $this->docxParser->extractText($filePath);
        return $this->cvDataMapper->map($text);
    }
}
