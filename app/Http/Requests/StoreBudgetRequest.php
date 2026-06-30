<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:transaction_categories,id',
            'month' => 'required|date',
            'amount' => 'required|numeric|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'month.required' => 'Bulan harus dipilih.',
            'amount.required' => 'Jumlah budget harus diisi.',
            'amount.min' => 'Jumlah budget minimal 1.',
        ];
    }
}
