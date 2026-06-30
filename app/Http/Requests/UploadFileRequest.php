<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware/policy
    }

    public function rules(): array
    {
        $allowedTypes = config('upload.allowed_types', []);
        $mimes = implode(',', $allowedTypes);

        return [
            'file' => [
                'required',
                'file',
                "mimes:{$mimes}",
                'max:' . (config('upload.global_max_size', 52428800) / 1024), // in KB for validation
            ],
            'type' => [
                'sometimes',
                'string',
                'in:certificate,avatar,document,product',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File is required.',
            'file.file' => 'Invalid file upload.',
            'file.mimes' => 'File type not allowed. Supported: ' . implode(', ', config('upload.allowed_types', [])),
            'file.max' => 'File size exceeds the maximum limit.',
            'type.in' => 'Invalid upload type.',
        ];
    }
}
