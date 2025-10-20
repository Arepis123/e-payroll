<?php

namespace App\Livewire\Admin;

use App\Models\PayrollSubmission;
use App\Models\PayrollPayment;
use Livewire\Component;

class Salary extends Component
{
    public $stats = [];
    public $recentSubmissions = [];
    public $submissionType = 'single';
    public $selectedClient = '';
    public $selectedWorker = '';
    public $payPeriod = '';
    public $basicSalary = 3500.00;
    public $hoursWorked = 176;
    public $overtimeHours = 8;
    public $allowances = 200.00;
    public $deductions = 0.00;

    public function mount()
    {
        $this->loadStats();
        $this->loadRecentSubmissions();
    }

    protected function loadStats()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Total submissions this month
        $totalSubmissions = PayrollSubmission::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        // Total amount this month
        $totalAmount = PayrollSubmission::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('total_amount');

        // Completed submissions (paid)
        $completed = PayrollSubmission::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->where('status', 'paid')
            ->count();

        // Pending submissions
        $pending = PayrollSubmission::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->whereIn('status', ['pending_payment', 'draft', 'overdue'])
            ->count();

        $this->stats = [
            'total_submissions' => $totalSubmissions,
            'total_amount' => $totalAmount,
            'completed' => $completed,
            'pending' => $pending,
        ];
    }

    protected function loadRecentSubmissions()
    {
        $submissions = PayrollSubmission::with(['user', 'payment'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $this->recentSubmissions = $submissions->map(function ($submission) {
            $clientName = $submission->user
                ? $submission->user->name
                : 'Client ' . $submission->contractor_clab_no;

            $monthName = date('M', mktime(0, 0, 0, $submission->month, 1));
            $period = $monthName . ' ' . $submission->year;

            $status = match($submission->status) {
                'paid' => 'Completed',
                'pending_payment' => 'Pending',
                'overdue' => 'Pending',
                default => 'Draft',
            };

            $paymentStatus = $submission->payment && $submission->payment->status === 'completed'
                ? 'Paid'
                : 'Awaiting';

            return [
                'id' => 'PAY' . str_pad($submission->id, 6, '0', STR_PAD_LEFT),
                'client' => $clientName,
                'workers' => $submission->total_workers,
                'period' => $period,
                'amount' => $submission->total_amount,
                'status' => $status,
                'payment_status' => $paymentStatus,
            ];
        })->toArray();
    }

    public function calculateTotal()
    {
        $overtimeAmount = $this->overtimeHours * 25; // RM 25 per hour
        return $this->basicSalary + $overtimeAmount + $this->allowances - $this->deductions;
    }

    public function saveDraft()
    {
        // TODO: Implement save draft logic
        session()->flash('success', 'Draft saved successfully!');
    }

    public function proceedToPayment()
    {
        // TODO: Implement payment logic
        session()->flash('success', 'Proceeding to payment...');
    }

    public function render()
    {
        return view('livewire.admin.salary', [
            'totalPayment' => $this->calculateTotal(),
        ]);
    }
}
