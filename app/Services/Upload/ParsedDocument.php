<?php

declare(strict_types=1);

namespace App\Services\Upload;

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
