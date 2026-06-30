<?php

declare(strict_types=1);

namespace App\Services\Upload;

use Illuminate\Support\Facades\Log;

final class DocumentParserService
{
    /**
     * Parse document and extract certificate data.
     */
    public function parse(string $filePath, string $mimeType): ParsedDocument
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        try {
            return match ($extension) {
                'pdf' => $this->parsePdf($filePath),
                'jpeg', 'jpg', 'png', 'webp' => $this->parseImage($filePath),
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

    /**
     * Parse PDF and extract text.
     */
    private function parsePdf(string $filePath): ParsedDocument
    {
        // Check if PDF parser is available
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            Log::warning('Smalot PDF Parser not installed');
            return $this->parseImage($filePath);
        }

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        if (empty(trim($text))) {
            // PDF might be scanned - need OCR
            Log::info('PDF appears to be scanned, no text extracted');
            return $this->parseScannedPdf($filePath);
        }

        return $this->extractCertificateData($text);
    }

    /**
     * Parse scanned PDF with OCR.
     */
    private function parseScannedPdf(string $filePath): ParsedDocument
    {
        // For scanned PDFs, we need OCR - this would be done in a queue job
        // Return empty with flag that OCR is needed
        $document = ParsedDocument::empty();
        $document->setNeedsOcr(true);

        return $document;
    }

    /**
     * Parse image with OCR.
     */
    private function parseImage(string $filePath): ParsedDocument
    {
        // For images, we need OCR - this would be done in a queue job
        // Return empty with flag that OCR is needed
        $document = ParsedDocument::empty();
        $document->setNeedsOcr(true);

        return $document;
    }

    /**
     * Parse DOCX and extract text.
     */
    private function parseDocx(string $filePath): ParsedDocument
    {
        if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
            Log::warning('PhpWord not installed');
            return ParsedDocument::empty();
        }

        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= $this->extractTextFromElement($element) . "\n";
                }
            }

            return $this->extractCertificateData($text);
        } catch (\Exception $e) {
            Log::error('DOCX parsing failed', ['error' => $e->getMessage()]);
            return ParsedDocument::empty()->withError($e->getMessage());
        }
    }

    /**
     * Extract text from DOCX element recursively.
     */
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

    /**
     * Extract certificate data from text using patterns.
     */
    private function extractCertificateData(string $text): ParsedDocument
    {
        $document = new ParsedDocument();

        // Extract title
        $document->title = $this->extractTitle($text);

        // Extract issuer
        $document->issuer = $this->extractIssuer($text);

        // Extract issue date
        $document->issueDate = $this->extractDate($text);

        // Extract credential ID
        $document->credentialId = $this->extractCredentialId($text);

        // Extract expiry date
        $document->expiryDate = $this->extractExpiryDate($text);

        // Extract certificate number
        $document->certificateNumber = $this->extractCertificateNumber($text);

        // Extract verification URL
        $document->verificationUrl = $this->extractVerificationUrl($text);

        // Calculate confidence based on how many fields were extracted
        $filledFields = array_filter([
            $document->title,
            $document->issuer,
            $document->issueDate,
        ]);

        $document->confidence = count($filledFields) / 3;

        return $document;
    }

    /**
     * Extract certificate title.
     */
    private function extractTitle(string $text): ?string
    {
        $lines = explode("\n", $text);
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines);

        $keywords = [
            'certificate', 'certification', 'certified', 'qualification', 'badge',
            'aws', 'azure', 'gcp', 'google cloud', 'microsoft', 'cisco', 'comptia',
            'oracle', 'ibm', 'hashicorp', 'kubernetes', 'docker', 'linux', 'scrum',
            'agile', 'pmp', 'itil', 'cissp', 'ceh', 'oscp', 'dicoding', 'coursera',
        ];

        foreach ($lines as $line) {
            if (strlen($line) < 5 || strlen($line) > 150) {
                continue;
            }

            $lineLower = strtolower($line);

            // Skip common non-title patterns
            if (preg_match('/^(this is|issued to|recipient|date|verifiable|http|www\.)/i', $line)) {
                continue;
            }

            // Skip name patterns
            if (preg_match('/^[A-Z][a-z]+\s+[A-Z][a-z]+$/', $line) && strlen($line) < 40) {
                continue;
            }

            foreach ($keywords as $keyword) {
                if (stripos($lineLower, $keyword) !== false) {
                    return $this->cleanTitle($line);
                }
            }
        }

        return null;
    }

    /**
     * Extract certificate issuer.
     */
    private function extractIssuer(string $text): ?string
    {
        $issuers = [
            'Amazon Web Services' => ['amazon web services', 'aws'],
            'Microsoft' => ['microsoft'],
            'Google Cloud' => ['google cloud', 'gcp', 'google'],
            'Cisco' => ['cisco systems', 'cisco networking academy', 'cisco'],
            'CompTIA' => ['comptia'],
            'Oracle' => ['oracle'],
            'IBM' => ['ibm'],
            'Coursera' => ['coursera'],
            'Udemy' => ['udemy'],
            'LinkedIn Learning' => ['linkedin learning', 'linkedin'],
            'edX' => ['edx'],
            'Kaggle' => ['kaggle'],
            'HashiCorp' => ['hashicorp'],
            'Dicoding' => ['dicoding'],
            'AWS' => ['aws certified'],
        ];

        $textLower = strtolower($text);

        foreach ($issuers as $name => $patterns) {
            foreach ($patterns as $pattern) {
                if (strpos($textLower, $pattern) !== false) {
                    return $name;
                }
            }
        }

        return null;
    }

    /**
     * Extract date from text.
     */
    private function extractDate(string $text): ?string
    {
        $months = [
            'january' => '01', 'february' => '02', 'march' => '03', 'april' => '04',
            'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08',
            'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12',
            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
            'jun' => '06', 'jul' => '07', 'aug' => '08', 'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12',
        ];

        // YYYY-MM-DD or YYYY/MM/DD
        if (preg_match('/(\d{4})[-/](\d{1,2})[-/](\d{1,2})/', $text, $m)) {
            if (checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
                return sprintf('%s-%02d-%02d', $m[1], $m[2], $m[3]);
            }
        }

        // DD Month YYYY or DD-Mon-YYYY
        if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/', $text, $m)) {
            $month = strtolower($m[2]);
            if (isset($months[$month]) && checkdate((int)$months[$month], (int)$m[1], (int)$m[3])) {
                return sprintf('%s-%s-%02d', $m[3], $months[$month], $m[1]);
            }
        }

        // Month YYYY (first day of month)
        if (preg_match('/([A-Za-z]+)\s+(\d{4})/', $text, $m)) {
            $month = strtolower($m[1]);
            if (isset($months[$month])) {
                return sprintf('%s-%s-01', $m[2], $months[$month]);
            }
        }

        // YYYY only (try to extract from context)
        if (preg_match('/(20[2-3]\d)/', $text, $m)) {
            return $m[1] . '-01-01';
        }

        return null;
    }

    /**
     * Extract credential ID.
     */
    private function extractCredentialId(string $text): ?string
    {
        // Pattern for common credential ID formats
        $patterns = [
            '/(?:credential|verification|badge|cert)[#:\s]+([A-Z0-9\-]+)/i',
            '/(?:ID|No\.|Number)[#:\s]+([A-Z0-9\-]+)/i',
            '/AWS(?:-|_)(?:ARN(?::)?|cert)(?::)?([A-Z0-9]+)/i',
            '/Credential ID[:\s]+([^\s\n]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return substr($m[1], 0, 100);
            }
        }

        return null;
    }

    /**
     * Extract expiry date.
     */
    private function extractExpiryDate(string $text): ?string
    {
        // Look for "valid until", "expires", "expiry" patterns
        if (preg_match('/(?:expires?|valid until|expiry date)[:\s]+(.+?)(?:\n|$)/i', $text, $m)) {
            return $this->extractDate($m[1]);
        }

        return null;
    }

    /**
     * Extract certificate number.
     */
    private function extractCertificateNumber(string $text): ?string
    {
        $patterns = [
            '/(?:certificate|cert)[#:\s]+([A-Z0-9\-]+)/i',
            '/(?:serial|series)[#:\s]+([A-Z0-9\-]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return substr($m[1], 0, 100);
            }
        }

        return null;
    }

    /**
     * Extract verification URL.
     */
    private function extractVerificationUrl(string $text): ?string
    {
        if (preg_match_all('/https?:\/\/[^\s<>"{}]+/', $text, $matches)) {
            foreach ($matches[0] as $url) {
                $url = rtrim($url, '.,;:');

                $credentialKeywords = ['credential', 'certificate', 'verify', 'validation', 'badge', 'aws', 'gcp'];
                foreach ($credentialKeywords as $kw) {
                    if (stripos($url, $kw) !== false) {
                        return $url;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Clean extracted title.
     */
    private function cleanTitle(string $title): string
    {
        $title = preg_replace('/^(certificate\s+of\s+|certificate\s+in\s+|certification\s+of\s+/i', '', $title);
        $title = preg_replace('/^(this is|here is|certifies that)/i', '', $title);

        return trim($title);
    }
}

/**
 * DTO for parsed document data.
 */
final class ParsedDocument
{
    public ?string $title = null;
    public ?string $issuer = null;
    public ?string $issueDate = null;
    public ?string $expiryDate = null;
    public ?string $credentialId = null;
    public ?string $certificateNumber = null;
    public ?string $verificationUrl = null;
    public float $confidence = 0.0;
    public bool $needsOcr = false;
    public ?string $error = null;
    public ?string $rawTextPreview = null;

    public static function empty(): self
    {
        return new self();
    }

    public function withError(string $error): self
    {
        $this->error = $error;
        return $this;
    }

    public function setNeedsOcr(bool $needs): void
    {
        $this->needsOcr = $needs;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'issuer' => $this->issuer,
            'issue_date' => $this->issueDate,
            'expiry_date' => $this->expiryDate,
            'credential_id' => $this->credentialId,
            'certificate_number' => $this->certificateNumber,
            'verification_url' => $this->verificationUrl,
            'confidence' => round($this->confidence, 2),
            'needs_ocr' => $this->needsOcr,
            'error' => $this->error,
            'raw_text_preview' => $this->rawTextPreview,
        ];
    }
}
