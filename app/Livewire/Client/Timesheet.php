<?php

namespace App\Livewire\Client;

use App\Models\PayrollSubmission;
use App\Models\PayrollWorker;
use App\Services\PayrollService;
use App\Services\ContractWorkerService;
use Livewire\Component;

class Timesheet extends Component
{
    protected PayrollService $payrollService;
    protected ContractWorkerService $contractWorkerService;

    public $workers = [];
    public $selectedWorkers = [];
    public $period;
    public $currentSubmission;
    public $stats;
    public $recentSubmissions;
    public $successMessage = '';
    public $errorMessage = '';

    public function boot(PayrollService $payrollService, ContractWorkerService $contractWorkerService)
    {
        $this->payrollService = $payrollService;
        $this->contractWorkerService = $contractWorkerService;
    }

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $clabNo = auth()->user()->contractor_clab_no;

        if (!$clabNo) {
            $this->errorMessage = 'No contractor CLAB number assigned to your account. Please contact administrator.';
            return;
        }

        // Get current payroll period info
        $this->period = $this->payrollService->getCurrentPayrollPeriod();

        // Get active contracted workers only
        $activeWorkers = $this->contractWorkerService->getActiveContractedWorkers($clabNo);

        // Get ALL submissions for this month to find all submitted workers
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $allSubmissionsThisMonth = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->with('workers')
            ->get();

        // Get IDs of workers already submitted
        $submittedWorkerIds = $allSubmissionsThisMonth->flatMap(function($submission) {
            return $submission->workers->pluck('worker_id');
        })->unique()->toArray();

        // Filter out workers who have already been submitted
        $remainingWorkers = $activeWorkers->filter(function($worker) use ($submittedWorkerIds) {
            return !in_array($worker->wkr_id, $submittedWorkerIds);
        });

        // Determine current submission status
        $currentStatus = 'draft';
        if ($allSubmissionsThisMonth->count() > 0) {
            $hasDraft = $allSubmissionsThisMonth->contains('status', 'draft');
            if ($hasDraft) {
                $currentStatus = 'draft';
            } else {
                $statuses = $allSubmissionsThisMonth->pluck('status')->unique();
                if ($statuses->contains('overdue')) {
                    $currentStatus = 'overdue';
                } elseif ($statuses->contains('pending_payment')) {
                    $currentStatus = 'pending_payment';
                } elseif ($statuses->contains('paid')) {
                    $currentStatus = 'paid';
                }
            }
        }

        $this->currentSubmission = (object)[
            'month' => $currentMonth,
            'year' => $currentYear,
            'status' => $currentStatus,
            'workers' => collect([]),
        ];

        // Prepare workers data
        $this->workers = $remainingWorkers->map(function($worker, $index) {
            return [
                'index' => $index,
                'worker_id' => $worker->wkr_id,
                'worker_name' => $worker->name,
                'worker_passport' => $worker->ic_number,
                'basic_salary' => $worker->basic_salary ?? 1700,
                'ot_normal_hours' => 0,
                'ot_rest_hours' => 0,
                'ot_public_hours' => 0,
                'included' => true,
            ];
        })->values()->toArray();

        // Initialize selected workers (all selected by default)
        $this->selectedWorkers = collect($this->workers)->pluck('worker_id')->toArray();

        // Update penalties
        $this->payrollService->updateOverduePenalties($clabNo);

        // Get recent submissions
        $this->recentSubmissions = $this->payrollService->getContractorSubmissions($clabNo)->take(5);

        // Get statistics
        $this->stats = $this->payrollService->getContractorStatistics($clabNo, $remainingWorkers->count());
    }

    public function toggleWorker($workerId)
    {
        if (in_array($workerId, $this->selectedWorkers)) {
            $this->selectedWorkers = array_values(array_diff($this->selectedWorkers, [$workerId]));
        } else {
            $this->selectedWorkers[] = $workerId;
        }
    }

    public function saveDraft()
    {
        return $this->saveSubmission('draft');
    }

    public function submitForPayment()
    {
        return $this->saveSubmission('submit');
    }

    private function saveSubmission($action)
    {
        $clabNo = auth()->user()->contractor_clab_no;

        if (!$clabNo) {
            $this->errorMessage = 'No contractor CLAB number assigned.';
            return;
        }

        // Validate
        $this->validate([
            'workers.*.worker_id' => 'required|string',
            'workers.*.worker_name' => 'required|string',
            'workers.*.worker_passport' => 'required|string',
            'workers.*.basic_salary' => 'required|numeric|min:1700',
            'workers.*.ot_normal_hours' => 'nullable|numeric|min:0',
            'workers.*.ot_rest_hours' => 'nullable|numeric|min:0',
            'workers.*.ot_public_hours' => 'nullable|numeric|min:0',
        ]);

        // Filter only selected workers
        $selectedWorkersData = collect($this->workers)->filter(function($worker) {
            return in_array($worker['worker_id'], $this->selectedWorkers);
        })->toArray();

        if (empty($selectedWorkersData)) {
            $this->errorMessage = 'Please select at least one worker to submit payroll.';
            return;
        }

        try {
            if ($action === 'draft') {
                $submission = $this->payrollService->savePayrollDraft($clabNo, $selectedWorkersData);
                $workerCount = count($selectedWorkersData);
                $this->successMessage = "Draft saved successfully. {$workerCount} worker(s) included.";
            } else {
                $submission = $this->payrollService->savePayrollSubmission($clabNo, $selectedWorkersData);
                $workerCount = count($selectedWorkersData);
                $this->successMessage = "Timesheet submitted successfully for {$submission->month_year}. {$workerCount} worker(s) included. Total amount: RM " . number_format($submission->total_amount, 2);
            }

            // Reload data
            $this->loadData();
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to save timesheet: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.client.timesheet')->layout('components.layouts.app', ['title' => __('Timesheet Management')]);
    }
}
