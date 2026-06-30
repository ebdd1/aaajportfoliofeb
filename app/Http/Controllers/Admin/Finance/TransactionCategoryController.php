<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransactionCategoryRequest;
use App\Http\Requests\UpdateTransactionCategoryRequest;
use App\Models\Finance\TransactionCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionCategoryController extends Controller
{
    public function index(Request $request)
    {
        $incomeCategories = TransactionCategory::income()->orderBy('name')->get();
        $expenseCategories = TransactionCategory::expense()->orderBy('name')->get();

        return inertia('Admin/Finance/TransactionCategories/Index', [
            'incomeCategories' => $incomeCategories,
            'expenseCategories' => $expenseCategories,
        ]);
    }

    public function store(StoreTransactionCategoryRequest $request): RedirectResponse
    {
        TransactionCategory::create($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Kategori berhasil dibuat.');
    }

    public function update(UpdateTransactionCategoryRequest $request, TransactionCategory $category): RedirectResponse
    {
        $category->update($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(TransactionCategory $category): RedirectResponse
    {
        if ($category->is_default) {
            return redirect()
                ->back()
                ->with('error', 'Kategori default tidak dapat dihapus.');
        }

        if ($category->transactions()->exists()) {
            return redirect()
                ->back()
                ->with('error', 'Kategori tidak dapat dihapus karena sudah digunakan oleh transaksi.');
        }

        $category->delete();

        return redirect()
            ->back()
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
