<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWalletRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:bank,ewallet,cash,savings',
            'balance' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'color' => 'nullable|string|size:7',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama dompet harus diisi.',
            'type.required' => 'Tipe dompet harus dipilih.',
            'type.in' => 'Tipe dompet tidak valid.',
        ];
    }
}
