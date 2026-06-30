<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetRequest;
use App\Models\Finance\Budget;
use App\Models\Finance\TransactionCategory;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->filled('month')
            ? Carbon::parse($request->month)
            : Carbon::now();

        $budgets = Budget::with('category')
            ->whereMonth('month', $month->month)
            ->whereYear('month', $month->year)
            ->get()
            ->sortBy(function ($budget) {
                return $budget->category?->name ?? '';
            });

        $expenseCategories = TransactionCategory::expense()->orderBy('name')->get();

        return inertia('Admin/Finance/Budgets/Index', [
            'budgets' => $budgets->values(),
            'expenseCategories' => $expenseCategories,
            'currentMonth' => $month->format('Y-m'),
        ]);
    }

    public function store(StoreBudgetRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['month'] = Carbon::parse($data['month'])->startOfMonth();

        Budget::updateOrCreate(
            [
                'category_id' => $data['category_id'],
                'month' => $data['month'],
            ],
            $data
        );

        return redirect()
            ->back()
            ->with('success', 'Budget berhasil disimpan.');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $budget->delete();

        return redirect()
            ->back()
            ->with('success', 'Budget berhasil dihapus.');
    }
}
