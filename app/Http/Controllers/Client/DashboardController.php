<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\PayrollSubmission;
use App\Services\ContractWorkerService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected ContractWorkerService $contractWorkerService;

    public function __construct(ContractWorkerService $contractWorkerService)
    {
        $this->contractWorkerService = $contractWorkerService;
    }

    public function index(Request $request)
    {
        // Get contractor CLAB number from authenticated user
        // Use username as fallback for matching with worker database
        $clabNo = $request->user()->contractor_clab_no ?? $request->user()->username;

        // If user doesn't have a CLAB number or username, show error
        if (!$clabNo) {
            return view('client.dashboard', [
                'error' => 'No contractor identifier assigned to your account. Please contact administrator.',
                'workers' => collect([]),
                'recentWorkers' => collect([]),
                'stats' => [
                    'total_workers' => 0,
                    'active_workers' => 0,
                    'expiring_soon' => 0,
                ],
                'expiringContracts' => collect([]),
                'paymentStats' => [
                    'this_month_amount' => 0,
                    'this_month_deadline' => null,
                    'pending_approvals' => 0,
                    'year_to_date_paid' => 0,
                ],
                'recentPayments' => collect([]),
            ]);
        }

        // Get contracted workers for this contractor
        $workers = $this->contractWorkerService->getContractedWorkers($clabNo);

        // Get active contracts for this contractor
        $activeContracts = $this->contractWorkerService->getActiveContractsByContractor($clabNo);

        // Get contracts expiring soon (within 30 days)
        $allExpiringContracts = $this->contractWorkerService->getExpiringContracts(30);

        // Filter expiring contracts for this contractor only
        $expiringContracts = $allExpiringContracts->filter(function($contract) use ($clabNo) {
            return $contract->con_ctr_clab_no === $clabNo;
        });

        // Calculate statistics
        $stats = [
            'total_workers' => $workers->count(),
            'active_workers' => $activeContracts->count(),
            'expiring_soon' => $expiringContracts->count(),
        ];

        // Get recent workers (limit to 4 for dashboard)
        $recentWorkers = $workers->take(4);

        // Get payment statistics
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Get this month's submission
        $thisMonthSubmission = PayrollSubmission::byContractor($clabNo)
            ->forMonth($currentMonth, $currentYear)
            ->first();

        // Get pending approvals (draft or pending_payment status)
        $pendingApprovals = PayrollSubmission::byContractor($clabNo)
            ->whereIn('status', ['draft', 'pending_payment'])
            ->count();

        // Get year to date paid amount
        $yearToDatePaid = PayrollSubmission::byContractor($clabNo)
            ->where('year', $currentYear)
            ->where('status', 'paid')
            ->sum('total_amount');

        $paymentStats = [
            'this_month_amount' => $thisMonthSubmission ? $thisMonthSubmission->total_with_penalty : 0,
            'this_month_deadline' => $thisMonthSubmission ? $thisMonthSubmission->payment_deadline : null,
            'this_month_workers' => $thisMonthSubmission ? $thisMonthSubmission->total_workers : 0,
            'pending_approvals' => $pendingApprovals,
            'year_to_date_paid' => $yearToDatePaid,
        ];

        // Get recent payments (last 3 months)
        $recentPayments = PayrollSubmission::byContractor($clabNo)
            ->with('payment')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(3)
            ->get();

        return view('client.dashboard', compact(
            'workers',
            'recentWorkers',
            'stats',
            'expiringContracts',
            'paymentStats',
            'recentPayments'
        ));
    }
}
