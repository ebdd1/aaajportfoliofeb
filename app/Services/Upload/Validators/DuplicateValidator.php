<?php

declare(strict_types=1);

namespace App\Services\Upload\Validators;

use App\Services\Upload\Validators\Contracts\FileValidatorInterface;
use App\Services\Upload\ValidationResult;
use Illuminate\Http\UploadedFile;

final class DuplicateValidator implements FileValidatorInterface
{
    public function validate(UploadedFile $file): ValidationResult
    {
        $hash = hash_file('sha256', $file->getPathname());

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

    public function getErrorMessage(): string
    {
        return 'Duplicate file detection failed.';
    }
}
