<?php

declare(strict_types=1);

namespace App\Services\Upload\Validators;

use App\Services\Upload\Validators\Contracts\FileValidatorInterface;
use App\Services\Upload\ValidationResult;
use Illuminate\Http\UploadedFile;

final class ImageValidator implements FileValidatorInterface
{
    private int $minWidth;
    private int $minHeight;
    private int $maxWidth;
    private int $maxHeight;

    public function __construct(
        int $minWidth = 100,
        int $minHeight = 100,
        int $maxWidth = 8000,
        int $maxHeight = 8000
    ) {
        $this->minWidth = $minWidth;
        $this->minHeight = $minHeight;
        $this->maxWidth = $maxWidth;
        $this->maxHeight = $maxHeight;
    }

    public function validate(UploadedFile $file): ValidationResult
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            return ValidationResult::valid();
        }

        $imageInfo = @getimagesize($file->getPathname());

        if ($imageInfo === false) {
            return ValidationResult::invalid(
                'INVALID_IMAGE',
                'File is not a valid image.'
            );
        }

        [$width, $height] = $imageInfo;

        if ($width < $this->minWidth || $height < $this->minHeight) {
            return ValidationResult::invalid(
                'IMAGE_TOO_SMALL',
                sprintf(
                    'Image dimensions too small. Minimum: %dx%d, got: %dx%d',
                    $this->minWidth,
                    $this->minHeight,
                    $width,
                    $height
                )
            );
        }

        if ($width > $this->maxWidth || $height > $this->maxHeight) {
            return ValidationResult::invalid(
                'IMAGE_TOO_LARGE',
                sprintf(
                    'Image dimensions too large. Maximum: %dx%d, got: %dx%d',
                    $this->maxWidth,
                    $this->maxHeight,
                    $width,
                    $height
                )
            );
        }

        return ValidationResult::valid();
    }

    public function getErrorMessage(): string
    {
        return 'Image validation failed.';
    }
}
