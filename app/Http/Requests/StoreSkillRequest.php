<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_number' => ['required', 'string', 'max:10'],
            'category_label' => ['required', 'string', 'max:255'],
            'category_title' => ['required', 'string', 'max:255'],
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['string', 'max:50'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_number.required' => 'Nomor kategori harus diisi.',
            'category_label.required' => 'Label kategori harus diisi.',
            'category_title.required' => 'Judul kategori harus diisi.',
            'tags.required' => 'Minimal satu tag harus ditambahkan.',
            'tags.min' => 'Minimal satu tag harus ditambahkan.',
        ];
    }
}
