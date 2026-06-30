<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'type' => 'nullable|in:service,digital_product,saas,physical',
            'status' => 'nullable|in:idea,building,active,paused,discontinued',
            'category_id' => 'required|exists:categories,id',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'thumbnail' => 'nullable|string|max:500',
            'thumbnail_path' => 'nullable|string|max:500',
            'demo_url' => 'nullable|url|max:500',
            'repo_url' => 'nullable|url|max:500',
            'landing_url' => 'nullable|url|max:500',
            'tags' => 'nullable|array',
            'is_public' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'version' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama produk harus diisi.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'demo_url.url' => 'URL demo tidak valid.',
            'repo_url.url' => 'URL repository tidak valid.',
            'landing_url.url' => 'URL landing page tidak valid.',
        ];
    }
}
