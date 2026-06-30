<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSavingsGoalRequest;
use App\Http\Requests\UpdateSavingsGoalRequest;
use App\Models\Finance\SavingsGoal;
use App\Models\Finance\Wallet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SavingsGoalController extends Controller
{
    public function index(Request $request)
    {
        $query = SavingsGoal::with('wallet');

        if ($request->filled('status')) {
            if ($request->status === 'achieved') {
                $query->where('is_achieved', true);
            } else {
                $query->where('is_achieved', false);
            }
        }

        $goals = $query->orderBy('is_achieved')
            ->orderBy('target_date', 'asc')
            ->paginate(20);

        $wallets = Wallet::active()->orderBy('name')->get();

        return inertia('Admin/Finance/SavingsGoals/Index', [
            'goals' => $goals,
            'wallets' => $wallets,
            'filters' => $request->only(['status']),
        ]);
    }

    public function create(): Response
    {
        $wallets = Wallet::active()->orderBy('name')->get();

        return inertia('Admin/Finance/SavingsGoals/Create', [
            'wallets' => $wallets,
        ]);
    }

    public function store(StoreSavingsGoalRequest $request): RedirectResponse
    {
        SavingsGoal::create($request->validated());

        return redirect()
            ->route('admin.finance.savings-goals.index')
            ->with('success', 'Target tabungan berhasil dibuat.');
    }

    public function edit(SavingsGoal $goal): Response
    {
        $goal->load('wallet');
        $wallets = Wallet::active()->orderBy('name')->get();

        return inertia('Admin/Finance/SavingsGoals/Edit', [
            'goal' => $goal,
            'wallets' => $wallets,
        ]);
    }

    public function update(UpdateSavingsGoalRequest $request, SavingsGoal $goal): RedirectResponse
    {
        $goal->update($request->validated());

        return redirect()
            ->route('admin.finance.savings-goals.index')
            ->with('success', 'Target tabungan berhasil diperbarui.');
    }

    public function addFunds(Request $request, SavingsGoal $goal): RedirectResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($goal, $request) {
            $goal->increment('current_amount', $request->amount);

            if ($goal->current_amount >= $goal->target_amount) {
                $goal->update(['is_achieved' => true]);
            }

            if ($goal->wallet_id) {
                $wallet = Wallet::findOrFail($goal->wallet_id);
                $wallet->decrement('balance', $request->amount);
            }
        });

        return redirect()
            ->back()
            ->with('success', 'Dana berhasil ditambahkan.');
    }

    public function destroy(SavingsGoal $goal): RedirectResponse
    {
        $goal->delete();

        return redirect()
            ->route('admin.finance.savings-goals.index')
            ->with('success', 'Target tabungan berhasil dihapus.');
    }
}
