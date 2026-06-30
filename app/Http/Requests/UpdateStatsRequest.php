<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'projects_count' => ['required', 'integer', 'min:0'],
            'semesters_count' => ['required', 'integer', 'min:0'],
            'experiences_count' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'projects_count.required' => 'Jumlah project harus diisi.',
            'projects_count.min' => 'Jumlah project tidak boleh negatif.',
            'semesters_count.required' => 'Jumlah semester harus diisi.',
            'semesters_count.min' => 'Jumlah semester tidak boleh negatif.',
            'experiences_count.required' => 'Jumlah pengalaman harus diisi.',
            'experiences_count.min' => 'Jumlah pengalaman tidak boleh negatif.',
        ];
    }
}
