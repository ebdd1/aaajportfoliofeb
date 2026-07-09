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
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

        // Initialize with safe defaults
        $messageStats = (object) ['total_messages' => 0, 'unread_messages' => 0];
        $invoiceStats = (object) ['outstanding_invoices' => 0, 'outstanding_amount' => 0];
        $financeStats = (object) ['income_this_month' => 0, 'expense_this_month' => 0];
        $totalBalance = 0;
        $revenueThisMonth = 0;

        try {
            // Message stats - single query (use false for PostgreSQL boolean compatibility)
            $messageStats = DB::table('messages')
                ->selectRaw('COUNT(*) as total_messages, SUM(CASE WHEN is_read = false THEN 1 ELSE 0 END) as unread_messages')
                ->first() ?? $messageStats;
        } catch (Throwable $e) {
            Log::warning('Dashboard: failed to fetch message stats', ['error' => $e->getMessage()]);
        }

        try {
            // Invoice stats - single query for unpaid
            $invoiceStats = DB::table('invoices')
                ->whereIn('status', ['sent', 'overdue'])
                ->selectRaw('COUNT(*) as outstanding_invoices, COALESCE(SUM(total), 0) as outstanding_amount')
                ->first() ?? $invoiceStats;
        } catch (Throwable $e) {
            Log::warning('Dashboard: failed to fetch invoice stats', ['error' => $e->getMessage()]);
        }

        try {
            // Finance stats - combined query for income/expense this month
            $financeStats = DB::table('transactions')
                ->whereBetween('date', [$startOfMonth, $endOfMonth])
                ->selectRaw("SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income_this_month")
                ->selectRaw("SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense_this_month")
                ->first() ?? $financeStats;
        } catch (Throwable $e) {
            Log::warning('Dashboard: failed to fetch finance stats', ['error' => $e->getMessage()]);
        }

        try {
            $totalBalance = Wallet::where('is_active', true)->sum('balance') ?? 0;
        } catch (Throwable $e) {
            Log::warning('Dashboard: failed to fetch wallet balance', ['error' => $e->getMessage()]);
        }

        try {
            // Revenue this month - from paid invoices
            $revenueThisMonth = DB::table('invoices')
                ->where('status', 'paid')
                ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
                ->sum('total') ?? 0;
        } catch (Throwable $e) {
            Log::warning('Dashboard: failed to fetch revenue', ['error' => $e->getMessage()]);
        }

        // These are fast count queries - wrap in try-catch too
        $totalProjects = $this->safeCount(fn () => Project::where('is_active', true)->count());
        $totalCerts = $this->safeCount(fn () => Certificate::where('is_active', true)->count());
        $totalSkills = $this->safeCount(fn () => Skill::where('is_active', true)->count());

        $recentMessages = [];
        try {
            $recentMessages = Message::latest()
                ->take(5)
                ->get(['id', 'name', 'email', 'message', 'is_read', 'created_at'])
                ->toArray();
        } catch (Throwable $e) {
            Log::warning('Dashboard: failed to fetch recent messages', ['error' => $e->getMessage()]);
        }

        $activeProducts = $this->safeCount(fn () => Product::where('status', 'active')->count());
        $newOrders = $this->safeCount(fn () => ProductOrder::where('status', 'new')->count());

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_messages' => $messageStats->total_messages ?? 0,
                'unread_messages' => $messageStats->unread_messages ?? 0,
                'total_projects' => $totalProjects,
                'total_certs' => $totalCerts,
                'total_skills' => $totalSkills,
            ],
            'recentMessages' => $recentMessages,
            'finance_summary' => [
                'total_balance' => $totalBalance,
                'income_this_month' => $financeStats->income_this_month ?? 0,
                'expense_this_month' => $financeStats->expense_this_month ?? 0,
                'outstanding_invoices' => $invoiceStats->outstanding_invoices ?? 0,
                'outstanding_amount' => $invoiceStats->outstanding_amount ?? 0,
            ],
            'product_summary' => [
                'active_products' => $activeProducts,
                'new_orders' => $newOrders,
                'revenue_this_month' => $revenueThisMonth,
            ],
        ]);
    }

    private function safeCount(callable $callback): int
    {
        try {
            return $callback();
        } catch (Throwable $e) {
            Log::warning('Dashboard: count query failed', ['error' => $e->getMessage()]);
            return 0;
        }
    }
}
