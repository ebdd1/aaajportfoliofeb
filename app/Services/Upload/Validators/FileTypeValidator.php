<?php

declare(strict_types=1);

namespace App\Services\Upload\Validators;

use App\Services\Upload\Validators\Contracts\FileValidatorInterface;
use App\Services\Upload\ValidationResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

final class FileTypeValidator implements FileValidatorInterface
{
    private array $allowedTypes;
    private array $mimeTypes;

    public function __construct()
    {
        $this->allowedTypes = config('upload.allowed_types', []);
        $this->mimeTypes = config('upload.mime_types', []);
    }

    public function validate(UploadedFile $file): ValidationResult
    {
        // Step 1: Check extension
        $extensionResult = $this->checkExtension($file);
        if (!$extensionResult->isValid()) {
            return $extensionResult;
        }

        // Step 2: Check MIME type
        return $this->checkMimeType($file);
    }

    private function checkExtension(UploadedFile $file): ValidationResult
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (empty($extension)) {
            return ValidationResult::invalid(
                'UNSUPPORTED_TYPE',
                'File extension is missing.'
            );
        }

        if (!in_array($extension, $this->allowedTypes, true)) {
            return ValidationResult::invalid(
                'UNSUPPORTED_TYPE',
                sprintf(
                    'File type not allowed. Supported: %s',
                    implode(', ', $this->allowedTypes)
                )
            );
        }

        return ValidationResult::valid();
    }

    private function checkMimeType(UploadedFile $file): ValidationResult
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $expectedMime = $this->mimeTypes[$extension] ?? null;

        if (!$expectedMime) {
            return ValidationResult::invalid(
                'MIME_MAPPING_ERROR',
                'MIME type mapping not found for extension.'
            );
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $actualMime = $finfo->file($file->getPathname());

        $normalizedMime = $this->normalizeMimeType($actualMime);
        $normalizedExpected = $this->normalizeMimeType($expectedMime);

        if ($normalizedMime !== $normalizedExpected) {
            Log::warning('MIME type mismatch', [
                'expected' => $expectedMime,
                'actual' => $actualMime,
                'filename' => $file->getClientOriginalName(),
            ]);

            return ValidationResult::invalid(
                'MIME_MISMATCH',
                sprintf(
                    'File content does not match extension. Expected %s but got %s.',
                    $expectedMime,
                    $actualMime
                ),
                [
                    'expected_mime' => $expectedMime,
                    'actual_mime' => $actualMime,
                ]
            );
        }

        return ValidationResult::valid();
    }

    private function normalizeMimeType(string $mime): string
    {
        $parts = explode(';', $mime);
        return trim($parts[0]);
    }

    public function getErrorMessage(): string
    {
        return 'File type validation failed.';
    }
}
