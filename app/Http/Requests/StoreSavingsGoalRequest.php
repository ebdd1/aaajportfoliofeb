<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSavingsGoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'current_amount' => 'nullable|numeric|min:0',
            'target_date' => 'nullable|date|after:today',
            'wallet_id' => 'nullable|exists:wallets,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|size:7',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama target tabungan harus diisi.',
            'target_amount.required' => 'Jumlah target harus diisi.',
            'target_amount.min' => 'Jumlah target minimal 1.',
            'target_date.after' => 'Tanggal target harus di masa depan.',
        ];
    }
}
