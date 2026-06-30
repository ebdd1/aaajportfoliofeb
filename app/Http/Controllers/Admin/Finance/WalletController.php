<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Models\Finance\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $wallets = Wallet::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return inertia('Admin/Finance/Wallets/Index', [
            'wallets' => $wallets,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return inertia('Admin/Finance/Wallets/Create');
    }

    public function store(StoreWalletRequest $request): RedirectResponse
    {
        Wallet::create($request->validated());

        return redirect()
            ->route('admin.finance.wallets.index')
            ->with('success', 'Dompet berhasil dibuat.');
    }

    public function edit(Wallet $wallet): Response
    {
        return inertia('Admin/Finance/Wallets/Edit', [
            'wallet' => $wallet,
        ]);
    }

    public function update(UpdateWalletRequest $request, Wallet $wallet): RedirectResponse
    {
        $wallet->update($request->validated());

        return redirect()
            ->route('admin.finance.wallets.index')
            ->with('success', 'Dompet berhasil diperbarui.');
    }

    public function destroy(Wallet $wallet): RedirectResponse
    {
        if ($wallet->transactions()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Dompet tidak dapat dihapus karena sudah memiliki transaksi.');
        }

        $wallet->delete();

        return redirect()
            ->route('admin.finance.wallets.index')
            ->with('success', 'Dompet berhasil dihapus.');
    }
}
