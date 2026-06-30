<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:income,expense',
            'category_id' => 'required|exists:transaction_categories,id',
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
            'reference_number' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'wallet_id.required' => 'Dompet harus dipilih.',
            'wallet_id.exists' => 'Dompet tidak ditemukan.',
            'type.required' => 'Tipe transaksi harus dipilih.',
            'type.in' => 'Tipe transaksi tidak valid.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori tidak ditemukan.',
            'amount.required' => 'Jumlah harus diisi.',
            'amount.min' => 'Jumlah minimal 1.',
            'description.required' => 'Deskripsi harus diisi.',
            'date.required' => 'Tanggal harus diisi.',
            'date.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
        ];
    }
}
