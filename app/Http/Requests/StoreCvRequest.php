<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'cv' => ['required', 'file', 'max:10240', 'mimes:pdf'],
        ];
    }

    public function messages(): array
    {
        return [
            'cv.required' => 'File CV harus diupload.',
            'cv.mimes' => 'File CV harus berformat PDF.',
            'cv.max' => 'Ukuran file CV maksimal 10MB.',
        ];
    }
}
