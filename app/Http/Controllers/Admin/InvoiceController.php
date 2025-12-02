<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollSubmission;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display all invoices (Admin can view all invoices from all contractors)
     */
    public function index(Request $request)
    {
        // Get all submissions (invoices) from all contractors
        $invoices = PayrollSubmission::with(['payment', 'user'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(15);

        // Calculate statistics
        $pendingInvoices = PayrollSubmission::whereIn('status', ['pending_payment', 'overdue'])->count();
        $paidInvoices = PayrollSubmission::where('status', 'paid')->count();
        $totalInvoiced = PayrollSubmission::sum('total_with_penalty');

        $stats = [
            'pending_invoices' => $pendingInvoices,
            'paid_invoices' => $paidInvoices,
            'total_invoiced' => $totalInvoiced,
        ];

        return view('admin.invoices', compact('invoices', 'stats'));
    }

    /**
     * Show individual invoice details (Admin can view any invoice)
     */
    public function show($id)
    {
        $invoice = PayrollSubmission::with(['workers.transactions', 'payment', 'user'])
            ->where('id', $id)
            ->firstOrFail();

        return view('admin.invoice-detail', compact('invoice'));
    }

    /**
     * Download invoice as PDF (Admin can download any invoice)
     */
    public function download($id)
    {
        $invoice = PayrollSubmission::with(['workers.transactions', 'payment', 'user'])
            ->where('id', $id)
            ->firstOrFail();

        $contractor = $invoice->user;

        $pdf = \PDF::loadView('admin.invoice-pdf', compact('invoice', 'contractor'))
            ->setPaper('a4', 'landscape');

        $filename = 'Invoice-INV-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT) . '-' . $invoice->month_year . '.pdf';

        return $pdf->download($filename);
    }
}
