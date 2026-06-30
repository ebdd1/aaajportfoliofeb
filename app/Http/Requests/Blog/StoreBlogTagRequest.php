<?php

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:50', 'unique:blog_tags,slug'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tag harus diisi.',
            'slug.unique' => 'Slug sudah digunakan. Gunakan slug lain.',
        ];
    }
}
