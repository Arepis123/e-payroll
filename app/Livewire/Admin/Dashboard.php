<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Models\PayrollSubmission;
use App\Models\PayrollWorker;
use App\Models\PayrollPayment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
    public $stats = [];
    public $recentPayments = [];
    public $chartData = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadRecentPayments();
        $this->loadChartData();
    }

    protected function loadStats()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastMonthYear = now()->subMonth()->year;

        // Get all clients
        $allClients = User::where('role', 'client')->get();
        $totalClients = $allClients->count();

        // Get clients who have submitted and paid for current month
        $clientsWithSubmission = PayrollSubmission::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->whereIn('status', ['paid', 'pending_payment'])
            ->distinct('contractor_clab_no')
            ->pluck('contractor_clab_no');

        // Clients without submission or payment this month
        $clientsWithoutSubmission = $allClients->whereNotIn('contractor_clab_no', $clientsWithSubmission)->count();

        // Previous month for comparison
        $clientsWithSubmissionLastMonth = PayrollSubmission::where('month', $lastMonth)
            ->where('year', $lastMonthYear)
            ->whereIn('status', ['paid', 'pending_payment'])
            ->distinct('contractor_clab_no')
            ->count();

        // Active workers (unique workers from all submissions)
        $activeWorkers = PayrollWorker::distinct('worker_id')->count('worker_id');

        // This month payments
        $thisMonthPayments = PayrollPayment::where('status', 'completed')
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->sum('amount');

        $lastMonthPayments = PayrollPayment::where('status', 'completed')
            ->whereYear('completed_at', $lastMonthYear)
            ->whereMonth('completed_at', $lastMonth)
            ->sum('amount');

        // Outstanding balance (pending + overdue submissions)
        $outstandingBalance = PayrollSubmission::whereIn('status', ['pending_payment', 'overdue'])
            ->sum('total_with_penalty');

        // Calculate growth
        $paymentsGrowth = $lastMonthPayments > 0
            ? round((($thisMonthPayments - $lastMonthPayments) / $lastMonthPayments) * 100, 1)
            : 0;

        $this->stats = [
            'clients_without_submission' => $clientsWithoutSubmission,
            'total_clients' => $totalClients,
            'clients_with_submission_count' => $clientsWithSubmission->count(),
            'active_workers' => $activeWorkers,
            'this_month_payments' => $thisMonthPayments,
            'outstanding_balance' => $outstandingBalance,
            'workers_growth' => 0, // TODO: Track worker growth over time
            'payments_growth' => $paymentsGrowth,
        ];
    }

    protected function loadRecentPayments()
    {
        $recentSubmissions = PayrollSubmission::with(['user', 'payment'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $this->recentPayments = $recentSubmissions->map(function ($submission) {
            $clientName = $submission->user
                ? $submission->user->name
                : 'Client ' . $submission->contractor_clab_no;

            $status = match($submission->status) {
                'paid' => 'completed',
                'pending_payment' => 'pending',
                'overdue' => 'pending',
                default => 'draft',
            };

            $date = $submission->payment && $submission->payment->completed_at
                ? $submission->payment->completed_at->format('M d, Y')
                : $submission->created_at->format('M d, Y');

            return [
                'client' => $clientName,
                'amount' => $submission->total_amount,
                'workers' => $submission->total_workers,
                'date' => $date,
                'status' => $status,
            ];
        })->toArray();
    }

    protected function loadChartData()
    {
        $currentYear = now()->year;
        $labels = [];
        $totalPayments = [];
        $numberOfPayments = [];

        // Get data for last 12 months
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->month;
            $year = $date->year;

            $labels[] = $date->format('M');

            // Total payment amount for the month
            $monthTotal = PayrollPayment::where('status', 'completed')
                ->whereYear('completed_at', $year)
                ->whereMonth('completed_at', $month)
                ->sum('amount');

            $totalPayments[] = (float) $monthTotal;

            // Number of payments for the month
            $monthCount = PayrollPayment::where('status', 'completed')
                ->whereYear('completed_at', $year)
                ->whereMonth('completed_at', $month)
                ->count();

            $numberOfPayments[] = $monthCount;
        }

        $this->chartData = [
            'labels' => $labels,
            'totalPayments' => $totalPayments,
            'numberOfPayments' => $numberOfPayments,
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
