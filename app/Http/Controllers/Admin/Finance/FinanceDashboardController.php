<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Budget;
use App\Models\Finance\Invoice;
use App\Models\Finance\SavingsGoal;
use App\Models\Finance\Transaction;
use App\Models\Finance\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceDashboardController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $totalBalance = Wallet::active()->sum('balance');

        $incomeThisMonth = Transaction::income()
            ->whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->sum('amount');

        $expenseThisMonth = Transaction::expense()
            ->whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->sum('amount');

        $incomeLastMonth = Transaction::income()
            ->whereBetween('date', [$lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth()])
            ->sum('amount');

        $expenseLastMonth = Transaction::expense()
            ->whereBetween('date', [$lastMonth->copy()->startOfMonth(), $lastMonth->copy()->endOfMonth()])
            ->sum('amount');

        $incomeChange = $incomeLastMonth > 0
            ? (($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth) * 100
            : 0;

        $expenseChange = $expenseLastMonth > 0
            ? (($expenseThisMonth - $expenseLastMonth) / $expenseLastMonth) * 100
            : 0;

        $wallets = Wallet::active()->orderBy('name')->get();

        $recentTransactions = Transaction::with(['wallet', 'category'])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unpaidInvoices = Invoice::unpaid()
            ->with('items')
            ->orderBy('due_date', 'asc')
            ->get();

        $budgetsThisMonth = Budget::with('category')
            ->whereMonth('month', $now->month)
            ->whereYear('month', $now->year)
            ->get();

        $savingsGoals = SavingsGoal::with('wallet')
            ->notAchieved()
            ->orderBy('target_date', 'asc')
            ->get();

        return inertia('Admin/Finance/Dashboard', [
            'wallets' => $wallets,
            'summary' => [
                'total_balance' => $totalBalance,
                'income_this_month' => $incomeThisMonth,
                'expense_this_month' => $expenseThisMonth,
                'income_last_month' => $incomeLastMonth,
                'expense_last_month' => $expenseLastMonth,
                'income_change_percent' => round($incomeChange, 1),
                'expense_change_percent' => round($expenseChange, 1),
            ],
            'recentTransactions' => $recentTransactions,
            'unpaidInvoices' => $unpaidInvoices,
            'budgetsThisMonth' => $budgetsThisMonth,
            'savingsGoals' => $savingsGoals,
            'outstandingInvoiceCount' => $unpaidInvoices->count(),
            'outstandingInvoiceAmount' => $unpaidInvoices->sum('total'),
        ]);
    }
}
