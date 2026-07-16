<?php

declare(strict_types=1);

namespace App\Services\Upload\Validators;

use App\Services\Upload\Validators\Contracts\FileValidatorInterface;
use App\Services\Upload\ValidationResult;
use Illuminate\Http\UploadedFile;

final class DocumentValidator implements FileValidatorInterface
{
    private array $allowedDocumentExtensions = ['pdf', 'doc', 'docx', 'txt'];

    public function validate(UploadedFile $file): ValidationResult
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, $this->allowedDocumentExtensions, true)) {
            return ValidationResult::valid();
        }

        if ($extension === 'pdf') {
            return $this->validatePdf($file);
        }

        if ($extension === 'docx') {
            return $this->validateDocx($file);
        }

        return ValidationResult::valid();
    }

    private function validatePdf(UploadedFile $file): ValidationResult
    {
        $handle = fopen($file->getPathname(), 'rb');
        if (!$handle) {
            return ValidationResult::invalid(
                'FILE_READ_ERROR',
                'Unable to read PDF file.'
            );
        }

        $header = fread($handle, 5);
        fclose($handle);

        if ($header === false || !str_starts_with($header, '%PDF-')) {
            return ValidationResult::invalid(
                'INVALID_PDF',
                'File is not a valid PDF document.'
            );
        }

        return ValidationResult::valid();
    }

    private function validateDocx(UploadedFile $file): ValidationResult
    {
        $handle = fopen($file->getPathname(), 'rb');
        if (!$handle) {
            return ValidationResult::invalid(
                'FILE_READ_ERROR',
                'Unable to read DOCX file.'
            );
        }

        $header = fread($handle, 4);
        fclose($handle);

        if ($header === false) {
            return ValidationResult::invalid(
                'FILE_READ_ERROR',
                'Unable to read DOCX file.'
            );
        }

        // DOCX files are ZIP archives starting with PK (50 4B 03 04)
        $headerBytes = unpack('H4', $header);
        if ($headerBytes[1] !== '504b0304') {
            return ValidationResult::invalid(
                'INVALID_DOCX',
                'File is not a valid DOCX document.'
            );
        }

        return ValidationResult::valid();
    }

    public function getErrorMessage(): string
    {
        return 'Document validation failed.';
    }
}
