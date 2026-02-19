<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinanceTransactionController extends Controller
{
    /**
     * Display all transactions with filtering and sorting
     */
    public function index(Request $request)
    {
        $query = Transaction::query();

        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }

        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('account_id') && $request->account_id) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%');
            });
        }

        // Get transactions with sorting
        $transactions = $query->with('account')
            ->orderBy('date', 'desc')
            ->paginate(20)
            ->through(fn($t) => [
                'id' => $t->id,
                'date' => $t->date->format('M d, Y'),
                'description' => $t->description,
                'amount' => $t->amount,
                'type' => $t->type,
                'category' => $t->category,
                'account_name' => $t->account?->name,
                'reference_number' => $t->reference_number,
                'notes' => $t->notes,
            ]);

        // Get unique categories for filter
        $categories = Transaction::distinct('category')->pluck('category');

        // Get accounts for filter
        $accounts = Account::active()->get(['id', 'name']);

        return Inertia::render('Dashboard/FIN/Transactions', [
            'transactions' => $transactions,
            'categories' => $categories,
            'accounts' => $accounts,
            'filters' => $request->only(['start_date', 'end_date', 'type', 'category', 'account_id', 'search']),
        ]);
    }

    /**
     * Show create transaction form
     */
    public function create()
    {
        $accounts = Account::active()->get(['id', 'code', 'name', 'type']);
        $categories = Transaction::distinct('category')->pluck('category');

        return Inertia::render('Dashboard/FIN/TransactionForm', [
            'accounts' => $accounts,
            'categories' => $categories,
        ]);
    }

    /**
     * Store a new transaction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense,transfer',
            'category' => 'required|string|max:100',
            'reference_number' => 'nullable|string|unique:transactions',
            'notes' => 'nullable|string',
        ]);

        // Prevent duplicate transactions (check for similar transaction in last hour)
        $duplicate = Transaction::where('account_id', $validated['account_id'])
            ->where('amount', $validated['amount'])
            ->where('description', $validated['description'])
            ->where('created_at', '>=', now()->subHour())
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['amount' => 'A similar transaction was created recently. Please check before submitting again.']);
        }

        try {
            Transaction::create($validated);
            return redirect()->route('finance.transactions.index')
                ->with('message', 'Transaction created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Show edit form for transaction
     */
    public function edit(Transaction $transaction)
    {
        $accounts = Account::active()->get(['id', 'code', 'name', 'type']);
        $categories = Transaction::distinct('category')->pluck('category');

        return Inertia::render('Dashboard/FIN/TransactionForm', [
            'transaction' => [
                'id' => $transaction->id,
                'account_id' => $transaction->account_id,
                'date' => $transaction->date->format('Y-m-d'),
                'description' => $transaction->description,
                'amount' => $transaction->amount,
                'type' => $transaction->type,
                'category' => $transaction->category,
                'reference_number' => $transaction->reference_number,
                'notes' => $transaction->notes,
            ],
            'accounts' => $accounts,
            'categories' => $categories,
        ]);
    }

    /**
     * Update transaction
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:income,expense,transfer',
            'category' => 'required|string|max:100',
            'reference_number' => 'nullable|string|unique:transactions,reference_number,' . $transaction->id,
            'notes' => 'nullable|string',
        ]);

        try {
            $transaction->update($validated);
            return redirect()->route('finance.transactions.index')
                ->with('message', 'Transaction updated successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete transaction
     */
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
            return back()->with('message', 'Transaction deleted successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Export transactions to CSV
     */
    public function export(Request $request)
    {
        $query = Transaction::query();

        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->where('date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('date', '<=', $request->end_date);
        }
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        $transactions = $query->with('account')->orderBy('date', 'desc')->get();

        $filename = 'transactions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            // Header row
            fputcsv($file, ['Date', 'Description', 'Category', 'Type', 'Amount', 'Account', 'Reference', 'Notes']);

            // Data rows
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->date->format('Y-m-d'),
                    $transaction->description,
                    $transaction->category,
                    $transaction->type,
                    $transaction->amount,
                    $transaction->account?->name,
                    $transaction->reference_number,
                    $transaction->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
