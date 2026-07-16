<?php

declare(strict_types=1);

namespace App\Services\Upload\Validators;

use App\Services\Upload\Validators\Contracts\FileValidatorInterface;
use App\Services\Upload\ValidationResult;
use Illuminate\Http\UploadedFile;

final class FileSizeValidator implements FileValidatorInterface
{
    private array $maxSizes;
    private int $globalMaxSize;

    public function __construct()
    {
        $this->maxSizes = config('upload.max_sizes', []);
        $this->globalMaxSize = config('upload.global_max_size', 50 * 1024 * 1024);
    }

    public function validate(UploadedFile $file): ValidationResult
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

    public function getErrorMessage(): string
    {
        return 'File size validation failed.';
    }
}
