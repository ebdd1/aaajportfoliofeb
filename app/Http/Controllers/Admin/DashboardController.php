<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Finance\Invoice;
use App\Models\Finance\Transaction;
use App\Models\Finance\Wallet;
use App\Models\Message;
use App\Models\Product\Product;
use App\Models\Product\ProductOrder;
use App\Models\Project;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Message stats - single query
        $messageStats = DB::table('messages')
            ->selectRaw('COUNT(*) as total_messages, SUM(CASE WHEN is_read = 0 THEN 1 ELSE 0 END) as unread_messages')
            ->first();

        // Invoice stats - single query for unpaid
        $invoiceStats = DB::table('invoices')
            ->whereIn('status', ['sent', 'overdue'])
            ->selectRaw('COUNT(*) as outstanding_invoices, COALESCE(SUM(total), 0) as outstanding_amount')
            ->first();

        // Finance stats - combined query for income/expense this month
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        $financeStats = DB::table('transactions')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income_this_month")
            ->selectRaw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense_this_month")
            ->first();

        $totalBalance = Wallet::where('is_active', true)->sum('balance');

        // Revenue this month - from paid invoices
        $revenueThisMonth = DB::table('invoices')
            ->where('status', 'paid')
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('total');

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_messages' => $messageStats->total_messages ?? 0,
                'unread_messages' => $messageStats->unread_messages ?? 0,
                'total_projects' => Project::where('is_active', true)->count(),
                'total_certs' => Certificate::where('is_active', true)->count(),
                'total_skills' => Skill::where('is_active', true)->count(),
            ],
            'recentMessages' => Message::latest()
                ->take(5)
                ->get(['id', 'name', 'email', 'message', 'is_read', 'created_at']),
            'finance_summary' => [
                'total_balance' => $totalBalance,
                'income_this_month' => $financeStats->income_this_month ?? 0,
                'expense_this_month' => $financeStats->expense_this_month ?? 0,
                'outstanding_invoices' => $invoiceStats->outstanding_invoices ?? 0,
                'outstanding_amount' => $invoiceStats->outstanding_amount ?? 0,
            ],
            'product_summary' => [
                'active_products' => Product::where('status', 'active')->count(),
                'new_orders' => ProductOrder::where('status', 'new')->count(),
                'revenue_this_month' => $revenueThisMonth ?? 0,
            ],
        ]);
    }
}
