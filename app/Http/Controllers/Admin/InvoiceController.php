<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollSubmission;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Show individual invoice details (Admin can view any invoice)
     */
    public function show(Request $request, $id)
    {
        $invoice = PayrollSubmission::with(['workers.transactions', 'payment', 'user'])
            ->where('id', $id)
            ->firstOrFail();

        return view('admin.invoice-detail', compact('invoice'));
    }

    /**
     * Download invoice as PDF (Admin can download any invoice)
     */
    public function download(Request $request, $id)
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
