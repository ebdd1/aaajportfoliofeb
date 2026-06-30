<?php

namespace App\Services;

use Smalot\PdfParser\Parser;

class CertificateParserService
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function parse(string $filePath): array
    {
        try {
            $pdf = $this->parser->parseFile($filePath);
            $text = $pdf->getText();

            if (empty($text) || strlen($text) < 50) {
                return ['error' => 'PDF kosong atau hasil scan. Gunakan PDF text-based.'];
            }

            $text = $this->cleanText($text);
            $lines = array_filter(array_map('trim', explode("\n", $text)));

            \Log::info('PDF TEXT', ['lines' => array_slice($lines, 0, 30)]);

            return [
                'title' => $this->extractTitle($lines),
                'issuer' => $this->extractIssuer($lines, $text),
                'issue_date' => $this->extractDate($text),
                'credential_url' => $this->extractCredentialUrl($text),
            ];
        } catch (\Exception $e) {
            \Log::error('Parse error', ['msg' => $e->getMessage()]);
            return ['error' => $e->getMessage()];
        }
    }

    private function cleanText(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text);
        $text = preg_replace('/[^\x20-\x7E\n]/', '', $text);
        return trim($text);
    }

    private function extractTitle(array $lines): ?string
    {
        // Strategy 1: Find line with keywords
        $keywords = ['certificate', 'certified', 'completion', 'achievement', 'course', 'training', 'belajar', 'sertifikat'];

        foreach ($lines as $i => $line) {
            $lower = strtolower($line);

            // Skip too short/long
            if (strlen($line) < 10 || strlen($line) > 150) continue;

            // Skip common headers
            if (preg_match('/^(this|hereby|issued|date|by|recipient|certificate of|sertifikat)/i', $line)) continue;

            // Skip person names (2-3 words, title case)
            if (preg_match('/^[A-Z][a-z]+\s+[A-Z][a-z]+(\s+[A-Z][a-z]+)?$/', $line)) continue;

            // Check if contains keywords
            foreach ($keywords as $kw) {
                if (stripos($lower, $kw) !== false) {
                    $title = preg_replace('/^(certificate of|sertifikat)\s*/i', '', $line);
                    return trim($title);
                }
            }
        }

        // Strategy 2: Find substantial line (likely title)
        foreach ($lines as $line) {
            if (strlen($line) >= 15 && strlen($line) <= 100) {
                if (!preg_match('/^[A-Z][a-z]+\s+[A-Z][a-z]+(\s+[A-Z][a-z]+)?$/', $line)) {
                    if (!preg_match('/^\d|^http|^www/i', $line)) {
                        return $line;
                    }
                }
            }
        }

        return null;
    }

    private function extractIssuer(array $lines, string $fullText): ?string
    {
        // Strategy 1: Known issuers
        $issuers = [
            'Amazon Web Services', 'Microsoft', 'Google', 'Cisco', 'Oracle', 'IBM',
            'Coursera', 'Udemy', 'LinkedIn', 'edX', 'Kaggle', 'HashiCorp',
            'Dicoding', 'Progate', 'Skill Academy', 'MySkill', 'BuildWith Angga',
            'ID Networkers', 'LMS ID Networkers', 'Sanbercode', 'Codepolitan',
        ];

        $lower = strtolower($fullText);
        foreach ($issuers as $issuer) {
            if (stripos($lower, strtolower($issuer)) !== false) {
                return $issuer;
            }
        }

        // Strategy 2: Pattern matching "issued by", "by", "from"
        foreach ($lines as $line) {
            if (preg_match('/(issued by|by|from|penerbit|oleh)[\s:]+([A-Za-z0-9\s&.]+)/i', $line, $m)) {
                $issuer = trim($m[2]);
                if (strlen($issuer) >= 3 && strlen($issuer) <= 100) {
                    return rtrim($issuer, '.,;:');
                }
            }
        }

        // Strategy 3: Look for company-like names
        foreach ($lines as $line) {
            if (strlen($line) >= 5 && strlen($line) <= 80) {
                if (preg_match('/^[A-Z][A-Za-z0-9\s&.,]+(Inc\.|Ltd\.|LLC|University|Academy|Institute)?$/i', $line)) {
                    if (!preg_match('/certificate|certified|completion|belajar/i', $line)) {
                        return trim($line);
                    }
                }
            }
        }

        return null;
    }

    private function extractDate(string $text): ?string
    {
        // Multiple date formats
        $patterns = [
            '/\b(january|february|march|april|may|june|july|august|september|october|november|december|januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\s+(\d{1,2}),?\s+(\d{4})\b/i',
            '/\b(\d{1,2})\s+(january|february|march|april|may|june|july|august|september|october|november|december|januari|februari|maret|april|mei|juni|juli|agustus|september|oktober|november|desember)\s+(\d{4})\b/i',
            '/\b(\d{4})-(\d{2})-(\d{2})\b/',
            '/\b(\d{1,2})\/(\d{1,2})\/(\d{4})\b/',
        ];

        $months = [
            'january' => '01', 'februari' => '02', 'march' => '03', 'april' => '04',
            'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08',
            'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12',
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'mei' => '05',
            'juni' => '06', 'juli' => '07', 'agustus' => '08', 'oktober' => '10', 'desember' => '12',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                if (isset($months[strtolower($m[1])])) {
                    $month = $months[strtolower($m[1])];
                    $day = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                    return "{$m[3]}-$month-$day";
                }
                if (isset($months[strtolower($m[2])])) {
                    $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                    $month = $months[strtolower($m[2])];
                    return "{$m[3]}-$month-$day";
                }
                if (strlen($m[1]) === 4) {
                    return $m[0];
                }
                if (isset($m[3])) {
                    $month = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                    $day = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                    return "{$m[3]}-$month-$day";
                }
            }
        }

        return null;
    }

    private function extractCredentialUrl(string $text): ?string
    {
        if (preg_match_all('/https?:\/\/[^\s<>"{}|\\\^\[\]`]+/i', $text, $matches)) {
            foreach ($matches[0] as $url) {
                $url = rtrim($url, '.,;:)');
                $keywords = ['credential', 'certificate', 'verify', 'badge', 'credly'];
                foreach ($keywords as $kw) {
                    if (stripos($url, $kw) !== false) {
                        return $url;
                    }
                }
            }
            if (!empty($matches[0])) {
                return rtrim($matches[0][0], '.,;:)');
            }
        }
        return null;
    }
}
