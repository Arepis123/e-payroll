<?php

namespace App\Livewire\Admin;

use App\Models\PayrollSubmission;
use Flux\Flux;
use Livewire\Component;

class SalaryDetail extends Component
{
    public PayrollSubmission $submission;
    public $workers = [];
    public $stats = [];
    public $previousSubmission = null;
    public $previousWorkers = [];
    public $previousOtStats = [];

    public function mount($id)
    {
        $this->submission = PayrollSubmission::with(['user', 'payment', 'workers.worker'])
            ->findOrFail($id);

        $this->loadWorkers();
        $this->calculateStats();
        $this->loadPreviousMonthOT();
    }

    protected function loadWorkers()
    {
        $this->workers = $this->submission->workers()
            ->with('worker')
            ->get();
    }

    protected function calculateStats()
    {
        $this->stats = [
            'total_workers' => $this->workers->count(),
            'total_regular_hours' => $this->workers->sum('regular_hours'),
            'total_ot_hours' => $this->workers->sum(function ($worker) {
                return $worker->ot_normal_hours + $worker->ot_rest_hours + $worker->ot_public_hours;
            }),
            'total_basic_salary' => $this->workers->sum('basic_salary'),
            'total_ot_pay' => $this->workers->sum('total_ot_pay'),
            'total_gross_salary' => $this->workers->sum('gross_salary'),
            'total_deductions' => $this->workers->sum('total_deductions'),
            'total_net_salary' => $this->workers->sum('net_salary'),
            'total_employer_contribution' => $this->workers->sum('total_employer_contribution'),
            'total_payment' => $this->workers->sum('total_payment'),
        ];
    }

    protected function loadPreviousMonthOT()
    {
        // Calculate previous month/year
        $currentMonth = $this->submission->month;
        $currentYear = $this->submission->year;

        $previousMonth = $currentMonth - 1;
        $previousYear = $currentYear;

        if ($previousMonth < 1) {
            $previousMonth = 12;
            $previousYear = $currentYear - 1;
        }

        // Find previous month's submission for the same contractor
        $this->previousSubmission = PayrollSubmission::with(['workers.worker'])
            ->where('contractor_clab_no', $this->submission->contractor_clab_no)
            ->where('month', $previousMonth)
            ->where('year', $previousYear)
            ->first();

        if ($this->previousSubmission) {
            $this->previousWorkers = $this->previousSubmission->workers;

            $this->previousOtStats = [
                'total_ot_hours' => $this->previousWorkers->sum(function ($worker) {
                    return $worker->ot_normal_hours + $worker->ot_rest_hours + $worker->ot_public_hours;
                }),
                'total_ot_pay' => $this->previousWorkers->sum('total_ot_pay'),
                'total_weekday_ot_hours' => $this->previousWorkers->sum('ot_normal_hours'),
                'total_weekday_ot_pay' => $this->previousWorkers->sum('ot_normal_pay'),
                'total_rest_ot_hours' => $this->previousWorkers->sum('ot_rest_hours'),
                'total_rest_ot_pay' => $this->previousWorkers->sum('ot_rest_pay'),
                'total_public_ot_hours' => $this->previousWorkers->sum('ot_public_hours'),
                'total_public_ot_pay' => $this->previousWorkers->sum('ot_public_pay'),
            ];
        } else {
            $this->previousOtStats = [
                'total_ot_hours' => 0,
                'total_ot_pay' => 0,
                'total_weekday_ot_hours' => 0,
                'total_weekday_ot_pay' => 0,
                'total_rest_ot_hours' => 0,
                'total_rest_ot_pay' => 0,
                'total_public_ot_hours' => 0,
                'total_public_ot_pay' => 0,
            ];
        }
    }

    public function downloadReceipt()
    {
        // TODO: Implement download receipt functionality
        Flux::toast(variant: 'info', text: 'Download receipt functionality coming soon!');
    }

    public function printPayslip()
    {
        // TODO: Implement print payslip functionality
        Flux::toast(variant: 'info', text: 'Print payslip functionality coming soon!');
    }

    public function markAsPaid()
    {
        // TODO: Implement mark as paid functionality (for manual payments)
        Flux::toast(variant: 'success', text: 'Manual payment marking functionality coming soon!');
    }

    public function sendReminder()
    {
        // TODO: Implement send reminder functionality
        Flux::toast(variant: 'success', text: 'Payment reminder sent to contractor!');
    }

    public function viewPaymentProof()
    {
        // TODO: Implement view payment proof functionality
        Flux::toast(variant: 'info', text: 'Payment proof viewing functionality coming soon!');
    }

    public function exportWorkerList()
    {
        // TODO: Implement export worker list functionality
        Flux::toast(variant: 'info', text: 'Export functionality coming soon!');
    }

    public function render()
    {
        return view('livewire.admin.salary-detail');
    }
}
