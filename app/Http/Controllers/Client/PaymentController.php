<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PayrollSubmission;
use App\Models\PayrollPayment;
use App\Services\BillplzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected BillplzService $billplzService;

    public function __construct(BillplzService $billplzService)
    {
        $this->billplzService = $billplzService;
    }

    /**
     * Create Billplz bill and redirect to payment
     */
    public function createPayment(Request $request, $submissionId)
    {
        $clabNo = $request->user()->contractor_clab_no;

        // Get payroll submission
        $submission = PayrollSubmission::with(['workers', 'payment'])
            ->where('id', $submissionId)
            ->where('contractor_clab_no', $clabNo)
            ->firstOrFail();

        // Check if already paid
        if ($submission->status === 'paid') {
            return redirect()->route('client.timesheet')
                ->with('error', 'This payroll has already been paid.');
        }

        // Check if payment already exists
        if ($submission->payment && $submission->payment->status === 'pending') {
            // Redirect to existing bill
            $url = $this->billplzService->getDirectPaymentUrl($submission->payment->billplz_url);
            return redirect($url);
        }

        // Check for penalty (overdue)
        $submission->updatePenalty();
        $submission->refresh();

        // Calculate total amount to pay (includes grand total with service charge + SST, plus penalty if overdue)
        $totalAmount = $submission->total_with_penalty;

        // Create Billplz bill
        $billData = [
            'email' => $request->user()->email,
            'name' => $request->user()->name ?? $request->user()->company_name,
            'amount' => $totalAmount,
            'callback_url' => route('billplz.callback'),
            'redirect_url' => route('client.payment.return', ['submission' => $submissionId]),
            'description' => "Payroll Payment - {$submission->month_year}",
            'reference_1_label' => 'Payroll ID',
            'reference_1' => $submission->id,
        ];

        $bill = $this->billplzService->createBill($billData);

        if (!$bill) {
            return back()->with('error', 'Failed to create payment. Please try again.');
        }

        // Create payment record
        $payment = PayrollPayment::create([
            'payroll_submission_id' => $submission->id,
            'payment_method' => 'billplz',
            'billplz_bill_id' => $bill['id'],
            'billplz_url' => $bill['url'],
            'amount' => $totalAmount,
            'status' => 'pending',
        ]);

        // Update submission status
        $submission->update(['status' => 'pending_payment']);

        // Redirect to Billplz payment page with auto-submit
        $paymentUrl = $this->billplzService->getDirectPaymentUrl($bill['url']);

        return redirect($paymentUrl);
    }

    /**
     * Handle Billplz callback webhook
     */
    public function callback(Request $request)
    {
        // Validate signature
        $billplzId = $request->input('id');
        $xSignature = $request->header('X-Signature');

        if (!$this->billplzService->validateSignature($billplzId, $xSignature)) {
            Log::warning('Billplz callback signature validation failed', [
                'bill_id' => $billplzId,
                'x_signature' => $xSignature,
            ]);

            return response('Invalid signature', 403);
        }

        // Find payment by billplz_bill_id
        $payment = PayrollPayment::where('billplz_bill_id', $billplzId)->first();

        if (!$payment) {
            Log::error('Payment not found for Billplz callback', [
                'bill_id' => $billplzId,
            ]);

            return response('Payment not found', 404);
        }

        // Check if already processed
        if ($payment->status === 'completed') {
            return response('OK');
        }

        // Get payment details
        $paid = $request->input('paid') === 'true';
        $state = $request->input('state');
        $amount = $request->input('paid_amount');
        $transactionId = $request->input('transaction_id');
        $transactionStatus = $request->input('transaction_status');

        // Store callback response
        $payment->payment_response = json_encode($request->all());
        $payment->transaction_id = $transactionId;

        if ($paid && $state === 'active' && $transactionStatus === 'completed') {
            // Payment successful
            $payment->markAsCompleted($request->all());

            Log::info('Billplz payment completed', [
                'bill_id' => $billplzId,
                'transaction_id' => $transactionId,
                'amount' => $amount,
            ]);
        } else {
            // Payment failed
            $payment->markAsFailed($request->all());

            Log::warning('Billplz payment failed', [
                'bill_id' => $billplzId,
                'paid' => $paid,
                'state' => $state,
                'transaction_status' => $transactionStatus,
            ]);
        }

        return response('OK');
    }

    /**
     * Handle return from Billplz payment page
     */
    public function return(Request $request, $submissionId)
    {
        $submission = PayrollSubmission::with('payment')->findOrFail($submissionId);

        // Payment still pending, check with Billplz first
        if ($submission->payment && $submission->payment->billplz_bill_id && $submission->payment->status === 'pending') {
            $bill = $this->billplzService->getBill($submission->payment->billplz_bill_id);

            if ($bill && $bill['paid']) {
                // Update payment status
                $submission->payment->markAsCompleted($bill);
                $submission->refresh();
            }
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            // User session expired, show guest views without requiring login
            if ($submission->payment && $submission->payment->status === 'completed') {
                return view('client.payment-success-guest', compact('submission'));
            } elseif ($submission->payment && $submission->payment->status === 'failed') {
                return redirect()->route('login')
                    ->with('error', 'Payment failed. Please login to try again.');
            } else {
                return redirect()->route('login')
                    ->with('info', 'Payment is being processed. Please login to check status.');
            }
        }

        // User is authenticated, show the appropriate view
        if ($submission->payment && $submission->payment->status === 'completed') {
            return view('client.payment-success', compact('submission'));
        } elseif ($submission->payment && $submission->payment->status === 'failed') {
            return view('client.payment-failed', compact('submission'));
        } else {
            return view('client.payment-pending', compact('submission'));
        }
    }
}
