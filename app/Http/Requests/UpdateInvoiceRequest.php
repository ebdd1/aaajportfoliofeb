<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'invoice_number' => 'nullable|string|max:20',
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_company' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'discount' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'wallet_id' => 'nullable|exists:wallets,id',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' => 'Nama klien harus diisi.',
            'issue_date.required' => 'Tanggal terbit harus diisi.',
            'due_date.required' => 'Tanggal jatuh tempo harus diisi.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo harus setelah tanggal terbit.',
            'items.required' => 'Minimal harus ada 1 item.',
            'items.min' => 'Minimal harus ada 1 item.',
            'items.*.description.required' => 'Deskripsi item harus diisi.',
            'items.*.quantity.required' => 'Jumlah item harus diisi.',
            'items.*.unit_price.required' => 'Harga satuan item harus diisi.',
        ];
    }
}
