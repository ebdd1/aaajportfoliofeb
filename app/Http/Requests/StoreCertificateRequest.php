<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'issuer' => ['required', 'string', 'max:255'],
            'issued_date' => ['required', 'date'],
            'credential_url' => ['nullable', 'url', 'max:500'],
            'file' => ['required', 'file', 'max:20480', 'mimes:pdf'],
            'image' => ['nullable', 'image', 'max:2048'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Nama sertifikat harus diisi.',
            'issuer.required' => 'Penerbit sertifikat harus diisi.',
            'issued_date.required' => 'Tanggal terbit harus diisi.',
            'file.required' => 'File PDF sertifikat harus diupload.',
            'file.mimes' => 'File harus berformat PDF.',
            'file.max' => 'Ukuran file maksimal 20 MB.',
        ];
    }
}