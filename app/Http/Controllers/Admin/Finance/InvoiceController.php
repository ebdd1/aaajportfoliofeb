<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Finance\Invoice;
use App\Models\Finance\InvoiceItem;
use App\Models\Finance\Transaction;
use App\Models\Finance\TransactionCategory;
use App\Models\Finance\Wallet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['items', 'wallet']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $wallets = Wallet::active()->orderBy('name')->get();

        return inertia('Admin/Finance/Invoices/Index', [
            'invoices' => $invoices,
            'wallets' => $wallets,
            'filters' => $request->only(['status']),
            'stats' => [
                'total_outstanding' => Invoice::unpaid()->sum('total'),
                'count_outstanding' => Invoice::unpaid()->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        $wallets = Wallet::active()->orderBy('name')->get();

        return inertia('Admin/Finance/Invoices/Create', [
            'invoice_number' => Invoice::generateNumber(),
            'wallets' => $wallets,
        ]);
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $items = $data['items'];
        unset($data['items']);

        // SECURITY: Calculate totals server-side (NEVER trust frontend input)
        $subtotal = collect($items)->sum(fn ($item) => $item['quantity'] * $item['unit_price']);
        $discount = $data['discount'] ?? 0;
        $taxPercentage = $data['tax_percentage'] ?? 0;
        $taxAmount = ($subtotal - $discount) * ($taxPercentage / 100);
        $total = $subtotal - $discount + $taxAmount;

        if (empty($data['invoice_number'])) {
            $data['invoice_number'] = Invoice::generateNumber();
        }

        // SECURITY: Create invoice with only fillable fields
        $invoice = Invoice::create($data);

        // SECURITY: Set calculated fields directly (not via mass assignment)
        $invoice->forceFill([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ])->save();

        foreach ($items as $index => $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['quantity'] * $item['unit_price'],
                'display_order' => $index,
            ]);
        }

        return redirect()
            ->route('admin.finance.invoices.index')
            ->with('success', 'Invoice berhasil dibuat.');
    }

    public function show(Invoice $invoice): Response
    {
        $invoice->load(['items', 'wallet', 'productOrder']);

        return inertia('Admin/Finance/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function edit(Invoice $invoice): Response
    {
        $invoice->load(['items', 'wallet']);
        $wallets = Wallet::active()->orderBy('name')->get();

        return inertia('Admin/Finance/Invoices/Edit', [
            'invoice' => $invoice,
            'wallets' => $wallets,
        ]);
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $data = $request->validated();

        $items = $data['items'];
        unset($data['items']);

        // SECURITY: Calculate totals server-side (NEVER trust frontend input)
        $subtotal = collect($items)->sum(fn ($item) => $item['quantity'] * $item['unit_price']);
        $discount = $data['discount'] ?? 0;
        $taxPercentage = $data['tax_percentage'] ?? 0;
        $taxAmount = ($subtotal - $discount) * ($taxPercentage / 100);
        $total = $subtotal - $discount + $taxAmount;

        // SECURITY: Update with only allowed fields, then set calculated values
        $invoice->update($data);
        $invoice->forceFill([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total' => $total,
        ])->save();

        $invoice->items()->delete();
        foreach ($items as $index => $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'amount' => $item['quantity'] * $item['unit_price'],
                'display_order' => $index,
            ]);
        }

        return redirect()
            ->route('admin.finance.invoices.index')
            ->with('success', 'Invoice berhasil diperbarui.');
    }

    public function markPaid(Request $request, Invoice $invoice): RedirectResponse
    {
        $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
        ]);

        // SECURITY: Get payment category ID dynamically (not hardcoded)
        $paymentCategory = TransactionCategory::where('slug', 'payment')
            ->orWhere('name', 'like', '%Pembayaran%')
            ->first();
        $categoryId = $paymentCategory?->id ?? 1; // Fallback to ID 1

        DB::transaction(function () use ($invoice, $request, $categoryId) {
            $invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
                'wallet_id' => $request->wallet_id,
            ]);

            Transaction::create([
                'wallet_id' => $request->wallet_id,
                'type' => 'income',
                'category_id' => $categoryId,
                'amount' => $invoice->total,
                'description' => 'Pembayaran Invoice ' . $invoice->invoice_number,
                'date' => now()->toDateString(),
                'reference_number' => $invoice->invoice_number,
            ]);

            $wallet = Wallet::findOrFail($request->wallet_id);
            $wallet->increment('balance', $invoice->total);
        });

        return redirect()
            ->back()
            ->with('success', 'Invoice ditandai sebagai lunas.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        if ($invoice->status !== 'draft') {
            return redirect()
                ->back()
                ->with('error', 'Hanya invoice dengan status draft yang dapat dihapus.');
        }

        $invoice->delete();

        return redirect()
            ->route('admin.finance.invoices.index')
            ->with('success', 'Invoice berhasil dihapus.');
    }

    public function downloadPdf(Invoice $invoice): Response
    {
        $invoice->load(['items']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
        ]);

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }
}
