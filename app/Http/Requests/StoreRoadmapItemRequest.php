<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoadmapItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:todo,in_progress,done,cancelled',
            'priority' => 'nullable|in:low,medium,high,critical',
            'category' => 'nullable|in:feature,bug,improvement,research',
            'estimated_hours' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul item harus diisi.',
        ];
    }
}
