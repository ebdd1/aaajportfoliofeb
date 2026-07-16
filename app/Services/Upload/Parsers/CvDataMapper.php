<?php

declare(strict_types=1);

namespace App\Services\Upload\Parsers;

use App\Services\Upload\ParsedDocument;

final class CvDataMapper
{
    public function map(string $text): ParsedDocument
    {
        $document = new ParsedDocument();

        $document->title = $this->extractTitle($text);
        $document->issuer = $this->extractIssuer($text);
        $document->issueDate = $this->extractDate($text);
        $document->expiryDate = $this->extractExpiryDate($text);
        $document->credentialId = $this->extractCredentialId($text);
        $document->certificateNumber = $this->extractCertificateNumber($text);
        $document->verificationUrl = $this->extractVerificationUrl($text);

        $filledFields = array_filter([
            $document->title,
            $document->issuer,
            $document->issueDate,
        ]);

        $document->confidence = count($filledFields) / 3;

        return $document;
    }

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

            if (preg_match('/^(this is|issued to|recipient|date|verifiable|http|www\.)/i', $line)) {
                continue;
            }

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

    public function extractDate(string $text): ?string
    {
        $months = [
            'january' => '01', 'february' => '02', 'march' => '03', 'april' => '04',
            'may' => '05', 'june' => '06', 'july' => '07', 'august' => '08',
            'september' => '09', 'october' => '10', 'november' => '11', 'december' => '12',
            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
            'jun' => '06', 'jul' => '07', 'aug' => '08', 'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12',
        ];

        if (preg_match('/(\d{4})[-/](\d{1,2})[-/](\d{1,2})/', $text, $m)) {
            if (checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
                return sprintf('%s-%02d-%02d', $m[1], $m[2], $m[3]);
            }
        }

        if (preg_match('/(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})/', $text, $m)) {
            $month = strtolower($m[2]);
            if (isset($months[$month]) && checkdate((int)$months[$month], (int)$m[1], (int)$m[3])) {
                return sprintf('%s-%s-%02d', $m[3], $months[$month], $m[1]);
            }
        }

        if (preg_match('/([A-Za-z]+)\s+(\d{4})/', $text, $m)) {
            $month = strtolower($m[1]);
            if (isset($months[$month])) {
                return sprintf('%s-%s-01', $m[2], $months[$month]);
            }
        }

        if (preg_match('/(20[2-3]\d)/', $text, $m)) {
            return $m[1] . '-01-01';
        }

        return null;
    }

    public function extractExpiryDate(string $text): ?string
    {
        if (preg_match('/(?:expires?|valid until|expiry date)[:\s]+(.+?)(?:\n|$)/i', $text, $m)) {
            return $this->extractDate($m[1]);
        }

        return null;
    }

    private function extractCredentialId(string $text): ?string
    {
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

    private function cleanTitle(string $title): string
    {
        $title = preg_replace('/^(certificate\s+of\s+|certificate\s+in\s+|certification\s+of\s+/i', '', $title);
        $title = preg_replace('/^(this is|here is|certifies that)/i', '', $title);

        return trim($title);
    }
}
