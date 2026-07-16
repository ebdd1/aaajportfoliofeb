<?php

declare(strict_types=1);

namespace App\Services\Upload\Validators;

use App\Services\Upload\Validators\Contracts\FileValidatorInterface;
use App\Services\Upload\ValidationResult;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

final class MagicNumberValidator implements FileValidatorInterface
{
    private array $magicNumbers;

    public function __construct()
    {
        $this->magicNumbers = config('upload.magic_numbers', []);
    }

    public function validate(UploadedFile $file): ValidationResult
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $expectedSignatures = $this->magicNumbers[$extension] ?? [];

        if (empty($expectedSignatures)) {
            return ValidationResult::valid();
        }

        $fileHandle = fopen($file->getPathname(), 'rb');
        if (!$fileHandle) {
            return ValidationResult::invalid(
                'FILE_READ_ERROR',
                'Unable to read file for validation.'
            );
        }

        $headerBytes = fread($fileHandle, 16);
        fclose($fileHandle);

        if ($headerBytes === false || strlen($headerBytes) < 4) {
            return ValidationResult::invalid(
                'CORRUPT_FILE',
                'File appears to be corrupted or too small.'
            );
        }

        $headerHex = $this->bytesToHex($headerBytes);

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

    private function bytesToHex(string $bytes): string
    {
        $hex = '';
        for ($i = 0; $i < strlen($bytes); $i++) {
            $hex .= sprintf('%02X', ord($bytes[$i]));
        }
        return $hex;
    }

    public function getErrorMessage(): string
    {
        return 'File magic number validation failed.';
    }
}
