<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinanceReportController extends Controller
{
    /**
     * Display reports page
     */
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $reportType = $request->input('type', 'income_statement');

        $reportData = $this->generateReport($reportType, $startDate, $endDate);

        return Inertia::render('Dashboard/FIN/Reports', [
            'report' => $reportData,
            'report_type' => $reportType,
            'date_range' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Generate report based on type
     */
    private function generateReport(string $type, string $startDate, string $endDate): array
    {
        return match($type) {
            'income_statement' => $this->generateIncomeStatement($startDate, $endDate),
            'balance_sheet' => $this->generateBalanceSheet(),
            'cash_flow' => $this->generateCashFlow($startDate, $endDate),
            'expense_by_category' => $this->generateExpenseByCategory($startDate, $endDate),
            'revenue_by_customer' => $this->generateRevenueReport(),
            default => [],
        };
    }

    /**
     * Generate Income Statement (P&L)
     */
    private function generateIncomeStatement(string $startDate, string $endDate): array
    {
        $revenue = Transaction::where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $expenses = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $netProfit = $revenue - $expenses;

        // Get breakdown by category
        $revenueByCategory = Transaction::where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->map(fn($item) => [
                'category' => $item->category,
                'amount' => round($item->total, 2),
            ]);

        $expensesByCategory = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->map(fn($item) => [
                'category' => $item->category,
                'amount' => round($item->total, 2),
            ]);

        return [
            'type' => 'income_statement',
            'title' => 'Income Statement (Profit & Loss)',
            'summary' => [
                'total_revenue' => round($revenue, 2),
                'total_expenses' => round($expenses, 2),
                'net_profit' => round($netProfit, 2),
            ],
            'revenue_by_category' => $revenueByCategory,
            'expenses_by_category' => $expensesByCategory,
        ];
    }

    /**
     * Generate Balance Sheet
     */
    private function generateBalanceSheet(): array
    {
        // Simplified balance sheet - in production would link to account balances
        return [
            'type' => 'balance_sheet',
            'title' => 'Balance Sheet',
            'summary' => [
                'total_assets' => 0,
                'total_liabilities' => 0,
                'total_equity' => 0,
            ],
            'assets' => [],
            'liabilities' => [],
            'equity' => [],
        ];
    }

    /**
     * Generate Cash Flow Statement
     */
    private function generateCashFlow(string $startDate, string $endDate): array
    {
        // Get daily cash flow
        $cashFlow = Transaction::selectRaw("DATE(date) as date, SUM(CASE WHEN type='income' THEN amount ELSE -amount END) as net_flow")
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($item) => [
                'date' => $item->date,
                'amount' => round($item->net_flow, 2),
            ]);

        $totalInflow = Transaction::where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $totalOutflow = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        return [
            'type' => 'cash_flow',
            'title' => 'Cash Flow Statement',
            'summary' => [
                'total_inflow' => round($totalInflow, 2),
                'total_outflow' => round($totalOutflow, 2),
                'net_cash_flow' => round($totalInflow - $totalOutflow, 2),
            ],
            'daily_flow' => $cashFlow,
        ];
    }

    /**
     * Generate Expense Report by Category
     */
    private function generateExpenseByCategory(string $startDate, string $endDate): array
    {
        $expensesByCategory = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get()
            ->map(fn($item) => [
                'category' => $item->category,
                'amount' => round($item->total, 2),
                'percentage' => 0, // Will be calculated in frontend
            ]);

        $totalExpenses = $expensesByCategory->sum('amount');

        // Calculate percentages
        $expensesByCategory = $expensesByCategory->map(function ($item) use ($totalExpenses) {
            return array_merge($item, [
                'percentage' => $totalExpenses > 0 ? round(($item['amount'] / $totalExpenses) * 100, 2) : 0,
            ]);
        });

        return [
            'type' => 'expense_by_category',
            'title' => 'Expense Report by Category',
            'summary' => [
                'total_expenses' => round($totalExpenses, 2),
            ],
            'categories' => $expensesByCategory,
        ];
    }

    /**
     * Generate Revenue Report
     */
    private function generateRevenueReport(): array
    {
        // Placeholder for revenue by customer report
        return [
            'type' => 'revenue_by_customer',
            'title' => 'Revenue Report by Customer',
            'summary' => [
                'total_revenue' => 0,
            ],
            'customers' => [],
        ];
    }

    /**
     * Export report to CSV
     */
    public function exportReport(Request $request)
    {
        $type = $request->input('type', 'income_statement');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $filename = $type . '_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($type, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            match($type) {
                'income_statement' => $this->exportIncomeStatement($file, $startDate, $endDate),
                'expense_by_category' => $this->exportExpenseByCategory($file, $startDate, $endDate),
                default => null,
            };

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export income statement to CSV
     */
    private function exportIncomeStatement($file, string $startDate, string $endDate): void
    {
        fputcsv($file, ['Income Statement']);
        fputcsv($file, ['Period: ' . $startDate . ' to ' . $endDate]);
        fputcsv($file, []);
        fputcsv($file, ['Category', 'Amount']);

        $revenue = Transaction::where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        fputcsv($file, ['REVENUE']);
        foreach ($revenue as $item) {
            fputcsv($file, [$item->category, $item->total]);
        }

        fputcsv($file, []);
        $expenses = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        fputcsv($file, ['EXPENSES']);
        foreach ($expenses as $item) {
            fputcsv($file, [$item->category, $item->total]);
        }
    }

    /**
     * Export expense by category to CSV
     */
    private function exportExpenseByCategory($file, string $startDate, string $endDate): void
    {
        fputcsv($file, ['Expense Report by Category']);
        fputcsv($file, ['Period: ' . $startDate . ' to ' . $endDate]);
        fputcsv($file, []);
        fputcsv($file, ['Category', 'Amount', 'Percentage']);

        $expenses = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $totalExpenses = $expenses->sum('total');

        foreach ($expenses as $item) {
            $percentage = $totalExpenses > 0 ? ($item->total / $totalExpenses) * 100 : 0;
            fputcsv($file, [$item->category, $item->total, round($percentage, 2) . '%']);
        }
    }
}
