<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'nullable|string|max:50',
            'client_company' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'quoted_price' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk harus dipilih.',
            'product_id.exists' => 'Produk tidak ditemukan.',
            'client_name.required' => 'Nama klien harus diisi.',
            'client_email.required' => 'Email klien harus diisi.',
            'client_email.email' => 'Format email tidak valid.',
        ];
    }
}
