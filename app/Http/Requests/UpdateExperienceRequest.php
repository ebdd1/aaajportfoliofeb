<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExperienceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'period' => ['required', 'string', 'max:100'],
            'role' => ['required', 'string', 'max:255'],
            'organization' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'period.required' => 'Periode harus diisi.',
            'role.required' => 'Posisi/jabatan harus diisi.',
            'organization.required' => 'Nama organisasi/company harus diisi.',
        ];
    }
}
