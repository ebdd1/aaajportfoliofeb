<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'from_wallet_id' => 'required|different:to_wallet_id|exists:wallets,id',
            'to_wallet_id' => 'required|different:from_wallet_id|exists:wallets,id',
            'amount' => 'required|numeric|min:1',
            'fee' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date|before_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'from_wallet_id.required' => 'Dompet asal harus dipilih.',
            'from_wallet_id.different' => 'Dompet asal dan tujuan harus berbeda.',
            'to_wallet_id.required' => 'Dompet tujuan harus dipilih.',
            'to_wallet_id.different' => 'Dompet asal dan tujuan harus berbeda.',
            'amount.required' => 'Jumlah transfer harus diisi.',
            'amount.min' => 'Jumlah minimal 1.',
            'date.required' => 'Tanggal harus diisi.',
        ];
    }
}
