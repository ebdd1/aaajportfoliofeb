<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransferRequest;
use App\Models\Finance\Transfer;
use App\Models\Finance\Wallet;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $query = Transfer::with(['fromWallet', 'toWallet']);

        if ($request->filled('wallet_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('from_wallet_id', $request->wallet_id)
                    ->orWhere('to_wallet_id', $request->wallet_id);
            });
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', Carbon::parse($request->date_from));
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', Carbon::parse($request->date_to));
        }

        $transfers = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $wallets = Wallet::active()->orderBy('name')->get();

        return inertia('Admin/Finance/Transfers/Index', [
            'transfers' => $transfers,
            'wallets' => $wallets,
            'filters' => $request->only(['wallet_id', 'date_from', 'date_to']),
        ]);
    }

    public function create(): Response
    {
        $wallets = Wallet::active()->orderBy('name')->get();

        return inertia('Admin/Finance/Transfers/Create', [
            'wallets' => $wallets,
        ]);
    }

    public function store(StoreTransferRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            Transfer::create($data);

            $fromWallet = Wallet::findOrFail($data['from_wallet_id']);
            $toWallet = Wallet::findOrFail($data['to_wallet_id']);

            $totalDebit = $data['amount'] + ($data['fee'] ?? 0);
            $fromWallet->decrement('balance', $totalDebit);
            $toWallet->increment('balance', $data['amount']);
        });

        return redirect()
            ->route('admin.finance.transfers.index')
            ->with('success', 'Transfer berhasil dibuat.');
    }

    public function destroy(Transfer $transfer): RedirectResponse
    {
        DB::transaction(function () use ($transfer) {
            $fromWallet = $transfer->fromWallet;
            $toWallet = $transfer->toWallet;

            $totalDebit = $transfer->amount + $transfer->fee;

            $fromWallet->increment('balance', $totalDebit);
            $toWallet->decrement('balance', $transfer->amount);

            $transfer->delete();
        });

        return redirect()
            ->route('admin.finance.transfers.index')
            ->with('success', 'Transfer berhasil dihapus.');
    }
}
