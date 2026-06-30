<?php

declare(strict_types=1);

namespace App\Services\Upload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

final class FileValidationService
{
    private array $allowedTypes;
    private array $mimeTypes;
    private array $magicNumbers;
    private array $maxSizes;
    private int $globalMaxSize;

    public function __construct()
    {
        $this->allowedTypes = config('upload.allowed_types', []);
        $this->mimeTypes = config('upload.mime_types', []);
        $this->magicNumbers = config('upload.magic_numbers', []);
        $this->maxSizes = config('upload.max_sizes', []);
        $this->globalMaxSize = config('upload.global_max_size', 50 * 1024 * 1024);
    }

    /**
     * Validate uploaded file through all layers.
     */
    public function validate(UploadedFile $file): ValidationResult
    {
        // Layer 1: Extension Check
        $extensionResult = $this->checkExtension($file);
        if (!$extensionResult->isValid()) {
            return $extensionResult;
        }

        // Layer 2: MIME Type Check
        $mimeResult = $this->checkMimeType($file);
        if (!$mimeResult->isValid()) {
            return $mimeResult;
        }

        // Layer 3: Magic Number Check (most authoritative)
        $magicResult = $this->checkMagicNumber($file);
        if (!$magicResult->isValid()) {
            return $magicResult;
        }

        // Layer 4: File Size Check
        $sizeResult = $this->checkFileSize($file);
        if (!$sizeResult->isValid()) {
            return $sizeResult;
        }

        // Layer 5: Duplicate Check
        $duplicateResult = $this->checkDuplicate($file);
        if (!$duplicateResult->isValid()) {
            return $duplicateResult;
        }

        return ValidationResult::valid();
    }

    /**
     * Layer 1: Check file extension.
     */
    public function checkExtension(UploadedFile $file): ValidationResult
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

    /**
     * Layer 2: Check MIME type using finfo (not browser-provided).
     */
    public function checkMimeType(UploadedFile $file): ValidationResult
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $expectedMime = $this->mimeTypes[$extension] ?? null;

        if (!$expectedMime) {
            return ValidationResult::invalid(
                'MIME_MAPPING_ERROR',
                'MIME type mapping not found for extension.'
            );
        }

        // Use finfo for authoritative MIME type detection
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $actualMime = $finfo->file($file->getPathname());

        // Handle MIME type variations
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

    /**
     * Layer 3: Check magic number / file signature.
     */
    public function checkMagicNumber(UploadedFile $file): ValidationResult
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $expectedSignatures = $this->magicNumbers[$extension] ?? [];

        if (empty($expectedSignatures)) {
            // No magic number configured for this type
            return ValidationResult::valid();
        }

        $fileHandle = fopen($file->getPathname(), 'rb');
        if (!$fileHandle) {
            return ValidationResult::invalid(
                'FILE_READ_ERROR',
                'Unable to read file for validation.'
            );
        }

        // Read first 16 bytes for magic number check
        $headerBytes = fread($fileHandle, 16);
        fclose($fileHandle);

        if ($headerBytes === false || strlen($headerBytes) < 4) {
            return ValidationResult::invalid(
                'CORRUPT_FILE',
                'File appears to be corrupted or too small.'
            );
        }

        $headerHex = $this->bytesToHex($headerBytes);

        // Check against all expected signatures for this type
        foreach ($expectedSignatures as $signature) {
            $signatureHex = str_replace(' ', '', $signature);
            if (str_starts_with($headerHex, strtolower($signatureHex))) {
                return ValidationResult::valid();
            }
        }

        Log::warning('Magic number validation failed', [
            'extension' => $extension,
            'expected_signatures' => $expectedSignatures,
            'actual_header' => $headerHex,
        ]);

        return ValidationResult::invalid(
            'MAGIC_INVALID',
            'File format is invalid or file appears corrupted.',
            ['header_hex' => $headerHex]
        );
    }

    /**
     * Layer 4: Check file size.
     */
    public function checkFileSize(UploadedFile $file): ValidationResult
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $maxSize = $this->maxSizes[$extension] ?? $this->globalMaxSize;
        $fileSize = $file->getSize();

        if ($fileSize > $maxSize) {
            return ValidationResult::invalid(
                'FILE_TOO_LARGE',
                sprintf(
                    'File size exceeds limit. Maximum size: %s.',
                    $this->formatBytes($maxSize)
                ),
                [
                    'max_size' => $maxSize,
                    'actual_size' => $fileSize,
                ]
            );
        }

        if ($fileSize === 0) {
            return ValidationResult::invalid(
                'EMPTY_FILE',
                'File is empty.'
            );
        }

        return ValidationResult::valid();
    }

    /**
     * Layer 5: Check for duplicate file (by hash).
     */
    public function checkDuplicate(UploadedFile $file): ValidationResult
    {
        $hash = hash_file('sha256', $file->getPathname());

        // Check if file with same hash exists
        $existingUpload = \App\Models\Upload::where('file_hash', $hash)->first();

        if ($existingUpload) {
            return ValidationResult::invalid(
                'DUPLICATE_FILE',
                'This file has already been uploaded.',
                [
                    'existing_upload_id' => $existingUpload->id,
                    'existing_uploaded_at' => $existingUpload->created_at,
                ]
            );
        }

        return ValidationResult::valid();
    }

    /**
     * Sanitize filename for security.
     */
    public function sanitizeFilename(string $filename): string
    {
        // Remove path traversal attempts
        $filename = str_replace(['..', '/', '\\', "\0"], '', $filename);

        // Remove dangerous characters
        $filename = preg_replace('/[^\w\-\.]+/', '_', $filename);

        // Limit length
        if (strlen($filename) > 200) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($name, 0, 200 - strlen($extension) - 1) . '.' . $extension;
        }

        return trim($filename, '_-.');
    }

    /**
     * Generate secure filename with UUID.
     */
    public function generateSecureFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $uuid = (string) \Illuminate\Support\Str::uuid();

        return $uuid . '.' . $extension;
    }

    /**
     * Get file hash.
     */
    public function getFileHash(UploadedFile $file): string
    {
        return hash_file('sha256', $file->getPathname());
    }

    /**
     * Check if file requires OCR processing.
     */
    public function requiresOcr(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $ocrTypes = ['pdf', 'jpeg', 'jpg', 'png', 'docx'];

        return in_array($extension, $ocrTypes, true);
    }

    /**
     * Convert bytes to hex string.
     */
    private function bytesToHex(string $bytes): string
    {
        $hex = '';
        for ($i = 0; $i < strlen($bytes); $i++) {
            $hex .= sprintf('%02X', ord($bytes[$i]));
        }
        return $hex;
    }

    /**
     * Normalize MIME type for comparison.
     */
    private function normalizeMimeType(string $mime): string
    {
        // Remove charset and other parameters
        $parts = explode(';', $mime);
        return trim($parts[0]);
    }

    /**
     * Format bytes to human readable.
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
