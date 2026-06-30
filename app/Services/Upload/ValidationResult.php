<?php

declare(strict_types=1);

namespace App\Services\Upload;

final class ValidationResult
{
    private function __construct(
        private readonly bool $valid,
        private readonly ?string $errorCode = null,
        private readonly ?string $errorMessage = null,
        private readonly ?array $details = null
    ) {}

    public static function valid(): self
    {
        return new self(true);
    }

    public static function invalid(
        string $errorCode,
        string $errorMessage,
        ?array $details = null
    ): self {
        return new self(false, $errorCode, $errorMessage, $details);
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function toArray(): array
    {
        if ($this->valid) {
            return ['valid' => true];
        }

        return [
            'valid' => false,
            'error' => [
                'code' => $this->errorCode,
                'message' => $this->errorMessage,
                'details' => $this->details,
            ],
        ];
    }
}
