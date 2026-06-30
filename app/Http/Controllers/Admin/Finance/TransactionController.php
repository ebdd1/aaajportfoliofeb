<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Finance\Transaction;
use App\Models\Finance\TransactionCategory;
use App\Models\Finance\Wallet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['wallet', 'category']);

        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', Carbon::parse($request->date_to));
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', "%{$request->search}%");
        }

        $transactions = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $wallets = Wallet::active()->orderBy('name')->get();
        $categories = TransactionCategory::orderBy('name')->get();

        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');

        return inertia('Admin/Finance/Transactions/Index', [
            'transactions' => $transactions,
            'wallets' => $wallets,
            'categories' => $categories,
            'filters' => $request->only(['wallet_id', 'category_id', 'type', 'date_from', 'date_to', 'search']),
            'totals' => [
                'income' => $totalIncome,
                'expense' => $totalExpense,
            ],
        ]);
    }

    public function create(): InertiaResponse
    {
        $wallets = Wallet::active()->orderBy('name')->get();
        $categories = TransactionCategory::orderBy('name')->get();

        return inertia('Admin/Finance/Transactions/Create', [
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $transaction = Transaction::create($data);

            $wallet = Wallet::findOrFail($data['wallet_id']);

            if ($data['type'] === 'income') {
                $wallet->increment('balance', $data['amount']);
            } else {
                $wallet->decrement('balance', $data['amount']);
            }
        });

        return redirect()
            ->route('admin.finance.transactions.index')
            ->with('success', 'Transaksi berhasil dibuat.');
    }

    public function edit(Transaction $transaction): InertiaResponse
    {
        $transaction->load(['wallet', 'category']);
        $wallets = Wallet::active()->orderBy('name')->get();
        $categories = TransactionCategory::orderBy('name')->get();

        return inertia('Admin/Finance/Transactions/Edit', [
            'transaction' => $transaction,
            'wallets' => $wallets,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($transaction, $data) {
            $oldWallet = Wallet::lockForUpdate()->findOrFail($transaction->wallet_id);
            $newWallet = ($transaction->wallet_id === $data['wallet_id'])
                ? $oldWallet
                : Wallet::lockForUpdate()->findOrFail($data['wallet_id']);

            $oldAmount = $transaction->amount;
            $oldType = $transaction->type;

            if ($oldType === 'income') {
                $oldWallet->decrement('balance', $oldAmount);
            } else {
                $oldWallet->increment('balance', $oldAmount);
            }

            $transaction->update($data);

            if ($data['type'] === 'income') {
                $newWallet->increment('balance', $data['amount']);
            } else {
                $newWallet->decrement('balance', $data['amount']);
            }
        });

        return redirect()
            ->route('admin.finance.transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        DB::transaction(function () use ($transaction) {
            $wallet = $transaction->wallet;

            if ($transaction->type === 'income') {
                $wallet->decrement('balance', $transaction->amount);
            } else {
                $wallet->increment('balance', $transaction->amount);
            }

            $transaction->delete();
        });

        return redirect()
            ->route('admin.finance.transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Transaction::with(['wallet', 'category']);

        if ($request->filled('wallet_id')) {
            $query->where('wallet_id', $request->wallet_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', Carbon::parse($request->date_to));
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transaksi-' . date('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Tanggal', 'Kategori', 'Tipe', 'Deskripsi', 'Dompet', 'Jumlah']);

            $query->orderBy('date', 'desc')->chunk(500, function ($transactions) use ($handle) {
                foreach ($transactions as $t) {
                    fputcsv($handle, [
                        $t->date->format('Y-m-d'),
                        $t->category->name,
                        $t->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                        $t->description,
                        $t->wallet->name,
                        number_format((float) $t->amount, 2, ',', '.')
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
