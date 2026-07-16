<?php

declare(strict_types=1);

namespace App\Services\Upload;

use App\Services\Upload\Validators\Contracts\FileValidatorInterface;
use App\Services\Upload\Validators\FileTypeValidator;
use App\Services\Upload\Validators\MagicNumberValidator;
use App\Services\Upload\Validators\FileSizeValidator;
use App\Services\Upload\Validators\DuplicateValidator;
use App\Services\Upload\Validators\ImageValidator;
use App\Services\Upload\Validators\DocumentValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

final class FileValidationService
{
    /** @var array<string, FileValidatorInterface> */
    private array $validators;

    public function __construct()
    {
        $this->validators = [
            'type' => new FileTypeValidator(),
            'magic' => new MagicNumberValidator(),
            'size' => new FileSizeValidator(),
            'duplicate' => new DuplicateValidator(),
            'image' => new ImageValidator(),
            'document' => new DocumentValidator(),
        ];
    }

    public function validate(UploadedFile $file): ValidationResult
    {
        // Layer 1: Type validation
        $result = $this->validators['type']->validate($file);
        if (!$result->isValid()) {
            return $result;
        }

        // Layer 2: Magic number validation
        $result = $this->validators['magic']->validate($file);
        if (!$result->isValid()) {
            return $result;
        }

        // Layer 3: Size validation
        $result = $this->validators['size']->validate($file);
        if (!$result->isValid()) {
            return $result;
        }

        // Layer 4: Duplicate validation
        $result = $this->validators['duplicate']->validate($file);
        if (!$result->isValid()) {
            return $result;
        }

        // Layer 5: Image validation (optional, only for images)
        $result = $this->validators['image']->validate($file);
        if (!$result->isValid()) {
            return $result;
        }

        // Layer 6: Document validation (optional, only for documents)
        $result = $this->validators['document']->validate($file);
        if (!$result->isValid()) {
            return $result;
        }

        return ValidationResult::valid();
    }

    public function sanitizeFilename(string $filename): string
    {
        $filename = str_replace(['..', '/', '\\', "\0"], '', $filename);
        $filename = preg_replace('/[^\w\-\.]+/', '_', $filename);

        if (strlen($filename) > 200) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $filename = substr($name, 0, 200 - strlen($extension) - 1) . '.' . $extension;
        }

        return trim($filename, '_-.');
    }

    public function generateSecureFilename(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        return (string) Str::uuid() . '.' . $extension;
    }

    public function getFileHash(UploadedFile $file): string
    {
        return hash_file('sha256', $file->getPathname());
    }

    public function requiresOcr(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $ocrTypes = ['pdf', 'jpeg', 'jpg', 'png', 'docx'];

        return in_array($extension, $ocrTypes, true);
    }
}
