<?php

namespace App\Http\Controllers\finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Bill;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinanceInvoiceBillController extends Controller
{
    /**
     * Display invoices and bills overview
     */
    public function index(Request $request)
    {
        // Fetch invoices with filtering
        $invoiceQuery = Invoice::query();
        if ($request->has('invoice_status') && $request->invoice_status) {
            $invoiceQuery->where('status', $request->invoice_status);
        }
        if ($request->has('start_date') && $request->start_date) {
            $invoiceQuery->where('issue_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $invoiceQuery->where('issue_date', '<=', $request->end_date);
        }

        $invoices = $invoiceQuery->orderBy('due_date', 'desc')
            ->paginate(15, ['*'], 'invoice_page')
            ->through(fn($i) => [
                'id' => $i->id,
                'invoice_number' => $i->invoice_number,
                'customer' => $i->customer,
                'issue_date' => $i->issue_date->format('M d, Y'),
                'due_date' => $i->due_date->format('M d, Y'),
                'total_amount' => $i->total_amount,
                'status' => $i->status,
                'days_until_due' => now()->diffInDays($i->due_date, false),
            ]);

        // Fetch bills with filtering
        $billQuery = Bill::query();
        if ($request->has('bill_status') && $request->bill_status) {
            $billQuery->where('status', $request->bill_status);
        }
        if ($request->has('start_date') && $request->start_date) {
            $billQuery->where('bill_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $billQuery->where('bill_date', '<=', $request->end_date);
        }

        $bills = $billQuery->orderBy('due_date', 'desc')
            ->paginate(15, ['*'], 'bill_page')
            ->through(fn($b) => [
                'id' => $b->id,
                'bill_number' => $b->bill_number,
                'vendor' => $b->vendor,
                'bill_date' => $b->bill_date->format('M d, Y'),
                'due_date' => $b->due_date->format('M d, Y'),
                'total_amount' => $b->total_amount,
                'status' => $b->status,
                'days_until_due' => now()->diffInDays($b->due_date, false),
            ]);

        $invoiceStatuses = ['draft', 'sent', 'paid', 'overdue'];
        $billStatuses = ['draft', 'received', 'approved', 'paid', 'overdue'];

        return Inertia::render('Dashboard/FIN/InvoicesBills', [
            'invoices' => $invoices,
            'bills' => $bills,
            'invoice_statuses' => $invoiceStatuses,
            'bill_statuses' => $billStatuses,
            'filters' => $request->only(['invoice_status', 'bill_status', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Show create invoice form
     */
    public function createInvoice()
    {
        return Inertia::render('Dashboard/FIN/InvoiceForm');
    }

    /**
     * Store new invoice
     */
    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'invoice_number' => 'required|string|unique:invoices',
            'customer' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after:issue_date',
            'total_amount' => 'required|numeric|min:0.01',
            'status' => 'required|in:draft,sent,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        try {
            Invoice::create($validated);
            return redirect()->route('finance.invoices-bills.index')
                ->with('message', 'Invoice created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark invoice as paid
     */
    public function markInvoicePaid(Invoice $invoice)
    {
        try {
            $invoice->update(['status' => 'paid']);
            return back()->with('message', 'Invoice marked as paid');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update invoice: ' . $e->getMessage()]);
        }
    }

    /**
     * Show create bill form
     */
    public function createBill()
    {
        return Inertia::render('Dashboard/FIN/BillForm');
    }

    /**
     * Store new bill
     */
    public function storeBill(Request $request)
    {
        $validated = $request->validate([
            'bill_number' => 'required|string|unique:bills',
            'vendor' => 'required|string|max:255',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after:bill_date',
            'total_amount' => 'required|numeric|min:0.01',
            'status' => 'required|in:draft,received,approved,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        try {
            Bill::create($validated);
            return redirect()->route('finance.invoices-bills.index')
                ->with('message', 'Bill created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create bill: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark bill as paid
     */
    public function markBillPaid(Bill $bill)
    {
        try {
            $bill->update(['status' => 'paid']);
            return back()->with('message', 'Bill marked as paid');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update bill: ' . $e->getMessage()]);
        }
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice(Invoice $invoice)
    {
        // This is a placeholder - implement PDF generation with a library like dompdf
        return response()->json(['message' => 'PDF generation not yet implemented'], 501);
    }

    /**
     * Download bill PDF
     */
    public function downloadBill(Bill $bill)
    {
        // This is a placeholder - implement PDF generation with a library like dompdf
        return response()->json(['message' => 'PDF generation not yet implemented'], 501);
    }
}
