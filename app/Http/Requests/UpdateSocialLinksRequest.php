<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSocialLinksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'links' => ['required', 'array', 'min:1'],
            'links.*.platform' => ['required', 'string'],
            'links.*.label' => ['required', 'string', 'max:50'],
            'links.*.url' => ['required', 'string', 'url', 'max:500'],
            'links.*.display_order' => ['nullable', 'integer', 'min:0'],
            'links.*.is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'links.required' => 'Minimal satu social link harus ditambahkan.',
            'links.*.platform.required' => 'Platform harus dipilih.',
            'links.*.label.required' => 'Label social link harus diisi.',
            'links.*.url.required' => 'URL social link harus diisi.',
            'links.*.url.url' => 'Format URL tidak valid.',
        ];
    }
}
