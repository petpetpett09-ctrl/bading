<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Bill;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FinanceDashboardController extends Controller
{
    /**
     * Display the finance dashboard with key metrics
     */
    public function index()
    {
        $currentMonth = now()->startOfMonth();
        $currentYear = now()->startOfYear();

        // Calculate current month revenue (sum of income transactions)
        $currentRevenue = Transaction::where('type', 'income')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('amount');

        // Calculate current month expenses (sum of expense transactions)
        $currentExpenses = Transaction::where('type', 'expense')
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->sum('amount');

        // Net profit for current month
        $netProfit = $currentRevenue - $currentExpenses;

        // Cash flow for last 30 days (net of income - expenses)
        $thirtyDaysAgo = now()->subDays(30);
        $cashFlow = Transaction::where('type', 'income')
            ->where('date', '>=', $thirtyDaysAgo)
            ->sum('amount')
            - Transaction::where('type', 'expense')
            ->where('date', '>=', $thirtyDaysAgo)
            ->sum('amount');

        // Outstanding invoices
        $outstandingInvoices = Invoice::whereIn('status', ['sent', 'overdue'])
            ->get();
        $outstandingInvoicesCount = $outstandingInvoices->count();
        $outstandingInvoicesAmount = $outstandingInvoices->sum('total_amount');

        // Upcoming bills (due in next 30 days)
        $upcomingBills = Bill::whereIn('status', ['approved', 'received'])
            ->where('due_date', '<=', now()->addDays(30))
            ->where('due_date', '>=', now())
            ->get();
        $upcomingBillsCount = $upcomingBills->count();
        $upcomingBillsAmount = $upcomingBills->sum('total_amount');

        // Get recent transactions (last 10)
        $recentTransactions = Transaction::with('account')
            ->orderBy('date', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'date' => $t->date->format('M d, Y'),
                'description' => $t->description,
                'amount' => $t->amount,
                'type' => $t->type,
                'category' => $t->category,
                'account_name' => $t->account?->name,
            ]);

        // Revenue trend (last 12 months)
        $revenueTrend = $this->getMonthlyTrend('income', 12);
        
        // Expense trend (last 12 months)
        $expenseTrend = $this->getMonthlyTrend('expense', 12);

        return Inertia::render('Dashboard/FIN/Dashboard', [
            'metrics' => [
                'current_revenue' => round($currentRevenue, 2),
                'current_expenses' => round($currentExpenses, 2),
                'net_profit' => round($netProfit, 2),
                'cash_flow_30_days' => round($cashFlow, 2),
                'outstanding_invoices_count' => $outstandingInvoicesCount,
                'outstanding_invoices_amount' => round($outstandingInvoicesAmount, 2),
                'upcoming_bills_count' => $upcomingBillsCount,
                'upcoming_bills_amount' => round($upcomingBillsAmount, 2),
            ],
            'recent_transactions' => $recentTransactions,
            'revenue_trend' => $revenueTrend,
            'expense_trend' => $expenseTrend,
        ]);
    }

    /**
     * Get monthly trend data for specified transaction type
     */
    private function getMonthlyTrend(string $type, int $months = 12): array
    {
        $data = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $amount = Transaction::where('type', $type)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $data[] = [
                'date' => $date->format('M Y'),
                'amount' => round($amount, 2),
            ];
        }

        return $data;
    }
}
