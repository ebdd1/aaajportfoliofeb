<?php

declare(strict_types=1);

namespace App\Services\Upload\Validators\Contracts;

use App\Services\Upload\ValidationResult;
use Illuminate\Http\UploadedFile;

interface FileValidatorInterface
{
    public function validate(UploadedFile $file): ValidationResult;

    public function getErrorMessage(): string;
}
