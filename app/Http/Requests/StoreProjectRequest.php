<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'tags' => ['required', 'array', 'min:1'],
            'tags.*' => ['string', 'max:50'],
            'repo_url' => ['nullable', 'url', 'max:500'],
            'demo_url' => ['nullable', 'url', 'max:500'],
            'repo_status' => ['required', 'in:available,coming_soon,private'],
            'image' => ['nullable', 'image', 'max:2048'],
            'is_featured' => ['boolean'],
            'display_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul project harus diisi.',
            'description.required' => 'Deskripsi project harus diisi.',
            'tags.required' => 'Minimal satu tag harus ditambahkan.',
            'tags.min' => 'Minimal satu tag harus ditambahkan.',
            'repo_status.required' => 'Status repository harus dipilih.',
        ];
    }
}
