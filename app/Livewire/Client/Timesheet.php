<?php

namespace App\Livewire\Client;

use App\Models\PayrollSubmission;
use App\Models\PayrollWorker;
use App\Services\PayrollService;
use App\Services\ContractWorkerService;
use App\Traits\LogsActivity;
use Livewire\Component;
use Livewire\Attributes\Reactive;

class Timesheet extends Component
{
    use LogsActivity;
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

    // Allow viewing specific month/year from query parameters
    public $targetMonth;
    public $targetYear;

    // Blocking logic for outstanding payments/drafts
    public $isBlocked = false;
    public $blockReasons = [];
    public $outstandingDrafts = [];
    public $overduePayments = [];
    public $missingSubmissions = [];

    // Transaction management
    public $showTransactionModal = false;
    public $currentWorkerIndex = null;
    public $transactions = [];
    public $newTransactionType = 'advance_payment';
    public $newTransactionAmount = '';
    public $newTransactionRemarks = '';

    public function boot(PayrollService $payrollService, ContractWorkerService $contractWorkerService)
    {
        $this->payrollService = $payrollService;
        $this->contractWorkerService = $contractWorkerService;
    }

    public function mount()
    {
        // Check if month/year parameters are passed from query string
        $this->targetMonth = request()->query('month');
        $this->targetYear = request()->query('year');

        $this->loadData();
    }

    public function loadData()
    {
        $clabNo = auth()->user()->contractor_clab_no;

        if (!$clabNo) {
            $this->errorMessage = 'No contractor CLAB number assigned to your account. Please contact administrator.';
            return;
        }

        // Check for outstanding drafts and unpaid invoices (only block for current month)
        // Don't block if viewing a past month to allow catching up
        $isViewingPastMonth = $this->targetMonth && $this->targetYear;
        if (!$isViewingPastMonth) {
            $this->checkOutstandingIssues($clabNo);
        }

        // Determine which month/year to load (specific period or current month)
        if ($this->targetMonth && $this->targetYear) {
            // Load specific period from query parameters
            $targetDate = \Carbon\Carbon::create($this->targetYear, $this->targetMonth, 1);
            $currentMonth = $this->targetMonth;
            $currentYear = $this->targetYear;

            // Get period info for the target month
            $this->period = [
                'month' => $currentMonth,
                'year' => $currentYear,
                'month_name' => $targetDate->format('F'),
                'deadline' => $targetDate->copy()->day(15)->addMonth(),
                'days_until_deadline' => now()->diffInDays($targetDate->copy()->day(15)->addMonth(), false),
            ];
        } else {
            // Get current payroll period info
            $this->period = $this->payrollService->getCurrentPayrollPeriod();
            $currentMonth = now()->month;
            $currentYear = now()->year;
        }

        // Get active contracted workers for the target period
        if ($this->targetMonth && $this->targetYear) {
            // Get workers who had active contracts during the target month
            $targetDate = \Carbon\Carbon::create($this->targetYear, $this->targetMonth, 1);
            $activeWorkers = $this->contractWorkerService->getContractedWorkers($clabNo)
                ->filter(function($worker) use ($targetDate) {
                    $contract = $worker->contracts()
                        ->where('con_end', '>=', $targetDate->startOfMonth()->toDateString())
                        ->where('con_start', '<=', $targetDate->endOfMonth()->toDateString())
                        ->first();
                    return $contract !== null;
                });
        } else {
            // Get currently active contracted workers only
            $activeWorkers = $this->contractWorkerService->getActiveContractedWorkers($clabNo);
        }

        // Get ALL submissions for this month to find all submitted workers

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
                'advance_payment' => 0,
                'deduction' => 0,
                'transactions' => [], // Store multiple transactions
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

    public function updated($propertyName)
    {
        // Auto-convert empty OT hours to 0
        if (preg_match('/^workers\.(\d+)\.(ot_normal_hours|ot_rest_hours|ot_public_hours)$/', $propertyName, $matches)) {
            $index = $matches[1];
            $field = $matches[2];

            if ($this->workers[$index][$field] === '' || $this->workers[$index][$field] === null) {
                $this->workers[$index][$field] = 0;
            }
        }
    }

    public function toggleWorker($workerId)
    {
        if (in_array($workerId, $this->selectedWorkers)) {
            $this->selectedWorkers = array_values(array_diff($this->selectedWorkers, [$workerId]));
        } else {
            $this->selectedWorkers[] = $workerId;
        }
    }

    public function openTransactionModal($workerIndex)
    {
        $this->currentWorkerIndex = $workerIndex;
        $this->transactions = $this->workers[$workerIndex]['transactions'] ?? [];
        $this->showTransactionModal = true;
        $this->resetNewTransaction();
    }

    public function closeTransactionModal()
    {
        $this->showTransactionModal = false;
        $this->currentWorkerIndex = null;
        $this->transactions = [];
        $this->resetNewTransaction();
    }

    public function resetNewTransaction()
    {
        $this->newTransactionType = 'advance_payment';
        $this->newTransactionAmount = '';
        $this->newTransactionRemarks = '';
        $this->resetValidation(['newTransactionAmount', 'newTransactionRemarks']);
    }

    public function addTransaction()
    {
        // Validate the new transaction
        $validated = $this->validate([
            'newTransactionType' => 'required|in:advance_payment,deduction',
            'newTransactionAmount' => 'required|numeric|min:0.01',
            'newTransactionRemarks' => 'required|string|min:3',
        ], [
            'newTransactionAmount.required' => 'Amount is required',
            'newTransactionAmount.min' => 'Amount must be greater than 0',
            'newTransactionRemarks.required' => 'Remarks are required',
            'newTransactionRemarks.min' => 'Remarks must be at least 3 characters',
        ]);

        // Create new transaction array
        $newTransaction = [
            'type' => $validated['newTransactionType'],
            'amount' => floatval($validated['newTransactionAmount']),
            'remarks' => $validated['newTransactionRemarks'],
        ];

        // CRITICAL: Update the worker's transactions array directly
        if ($this->currentWorkerIndex !== null) {
            $currentTransactions = $this->workers[$this->currentWorkerIndex]['transactions'] ?? [];
            $currentTransactions[] = $newTransaction;

            // Force Livewire reactivity by reassigning the entire workers array
            $workers = $this->workers;
            $workers[$this->currentWorkerIndex]['transactions'] = $currentTransactions;
            $this->workers = $workers;

            // Also update the modal's local transactions array
            $this->transactions = $currentTransactions;
        }

        // Log for debugging
        \Log::info('Transaction added', [
            'new_transaction' => $newTransaction,
            'worker_transactions' => $this->workers[$this->currentWorkerIndex]['transactions'] ?? [],
            'modal_transactions' => $this->transactions,
            'count' => count($this->transactions),
        ]);

        // Reset the form
        $this->resetNewTransaction();
    }

    public function removeTransaction($index)
    {
        if ($this->currentWorkerIndex !== null) {
            $currentTransactions = $this->workers[$this->currentWorkerIndex]['transactions'] ?? [];
            unset($currentTransactions[$index]);
            $currentTransactions = array_values($currentTransactions);

            // Force Livewire reactivity by reassigning the entire workers array
            $workers = $this->workers;
            $workers[$this->currentWorkerIndex]['transactions'] = $currentTransactions;
            $this->workers = $workers;

            // Update modal's local transactions
            $this->transactions = $currentTransactions;
        }
    }

    public function saveTransactions()
    {
        if ($this->currentWorkerIndex === null) {
            return;
        }

        // Get worker name before closing
        $workerName = $this->workers[$this->currentWorkerIndex]['worker_name'];

        // Save transactions to the worker - force array re-indexing
        $this->workers[$this->currentWorkerIndex]['transactions'] = array_values($this->transactions);

        // Calculate totals
        $totalAdvancePayment = collect($this->transactions)
            ->where('type', 'advance_payment')
            ->sum('amount');

        $totalDeduction = collect($this->transactions)
            ->where('type', 'deduction')
            ->sum('amount');

        // Update worker totals
        $this->workers[$this->currentWorkerIndex]['advance_payment'] = $totalAdvancePayment;
        $this->workers[$this->currentWorkerIndex]['deduction'] = $totalDeduction;

        // Close modal
        $this->closeTransactionModal();
        $this->successMessage = "Transactions saved successfully for {$workerName}. Total: Advance RM " . number_format($totalAdvancePayment, 2) . ", Deduction RM " . number_format($totalDeduction, 2);
    }

    public function saveDraft()
    {
        \Log::info('saveDraft called', [
            'workers_count' => count($this->workers),
            'selected_workers' => $this->selectedWorkers,
            'workers_data' => $this->workers,
        ]);
        return $this->saveSubmission('draft');
    }

    public function submitForPayment()
    {
        return $this->saveSubmission('submit');
    }

    public function submitDraftForPayment($submissionId)
    {
        $clabNo = auth()->user()->contractor_clab_no;

        if (!$clabNo) {
            $this->errorMessage = 'No contractor CLAB number assigned.';
            return;
        }

        try {
            // Find the draft submission
            $submission = PayrollSubmission::where('id', $submissionId)
                ->where('contractor_clab_no', $clabNo)
                ->where('status', 'draft')
                ->firstOrFail();

            // Update status to pending_payment
            $submission->update([
                'status' => 'pending_payment',
                'submitted_at' => now(),
            ]);

            $this->successMessage = "Draft submitted successfully for {$submission->month_year}. Total amount: RM " . number_format($submission->total_amount, 2);

            // Log activity
            $this->logTimesheetActivity(
                action: 'submitted',
                description: "Submitted payroll timesheet for {$submission->month_year} with {$submission->total_workers} workers (Total: RM " . number_format($submission->total_amount, 2) . ")",
                timesheet: $submission,
                properties: [
                    'period' => $submission->month_year,
                    'workers_count' => $submission->total_workers,
                    'total_amount' => $submission->total_amount,
                    'grand_total' => $submission->grand_total,
                ]
            );

            // Reload data
            $this->loadData();
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to submit draft: ' . $e->getMessage();
        }
    }

    private function saveSubmission($action)
    {
        \Log::info('saveSubmission called', ['action' => $action]);

        $clabNo = auth()->user()->contractor_clab_no;

        if (!$clabNo) {
            $this->errorMessage = 'No contractor CLAB number assigned.';
            \Log::error('No CLAB number');
            return;
        }

        // Validate
        try {
            $this->validate([
                'workers.*.worker_id' => 'required',
                'workers.*.worker_name' => 'required|string',
                'workers.*.worker_passport' => 'required|string',
                'workers.*.basic_salary' => 'required|numeric|min:1700',
                'workers.*.ot_normal_hours' => 'nullable|numeric|min:0',
                'workers.*.ot_rest_hours' => 'nullable|numeric|min:0',
                'workers.*.ot_public_hours' => 'nullable|numeric|min:0',
            ]);
        } catch (\Exception $e) {
            \Log::error('Validation failed', ['error' => $e->getMessage()]);
            $this->errorMessage = 'Validation failed: ' . $e->getMessage();
            return;
        }

        // Filter only selected workers
        $selectedWorkersData = collect($this->workers)->filter(function($worker) {
            return in_array($worker['worker_id'], $this->selectedWorkers);
        })->toArray();

        \Log::info('Selected workers', ['count' => count($selectedWorkersData), 'data' => $selectedWorkersData]);

        if (empty($selectedWorkersData)) {
            $this->errorMessage = 'Please select at least one worker to submit payroll.';
            \Log::error('No workers selected');
            return;
        }

        try {
            // Get the month and year from the period being viewed
            $month = $this->period['month'];
            $year = $this->period['year'];

            if ($action === 'draft') {
                \Log::info('Calling savePayrollDraft', ['month' => $month, 'year' => $year]);
                $submission = $this->payrollService->savePayrollDraft($clabNo, $selectedWorkersData, $month, $year);
                $workerCount = count($selectedWorkersData);
                $this->successMessage = "Draft saved successfully. {$workerCount} worker(s) included.";
                \Log::info('Draft saved', ['submission_id' => $submission->id]);

                // Log activity
                $this->logTimesheetActivity(
                    action: 'draft_saved',
                    description: "Saved payroll timesheet draft for {$submission->month_year} with {$workerCount} workers",
                    timesheet: $submission,
                    properties: [
                        'period' => $submission->month_year,
                        'workers_count' => $workerCount,
                    ]
                );
            } else {
                \Log::info('Calling savePayrollSubmission', ['month' => $month, 'year' => $year]);
                $submission = $this->payrollService->savePayrollSubmission($clabNo, $selectedWorkersData, $month, $year);
                $workerCount = count($selectedWorkersData);
                $this->successMessage = "Timesheet submitted successfully for {$submission->month_year}. {$workerCount} worker(s) included. Total amount: RM " . number_format($submission->total_amount, 2);
                \Log::info('Submission saved', ['submission_id' => $submission->id]);

                // Log activity
                $this->logTimesheetActivity(
                    action: 'submitted',
                    description: "Submitted payroll timesheet for {$submission->month_year} with {$workerCount} workers (Total: RM " . number_format($submission->total_amount, 2) . ")",
                    timesheet: $submission,
                    properties: [
                        'period' => $submission->month_year,
                        'workers_count' => $workerCount,
                        'total_amount' => $submission->total_amount,
                        'grand_total' => $submission->grand_total,
                    ]
                );
            }

            // Reload data
            $this->loadData();
        } catch (\Exception $e) {
            \Log::error('Failed to save', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $this->errorMessage = 'Failed to save timesheet: ' . $e->getMessage();
        }
    }

    protected function checkOutstandingIssues($clabNo)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $currentDate = now();

        // Reset blocking state
        $this->isBlocked = false;
        $this->blockReasons = [];
        $this->outstandingDrafts = [];
        $this->overduePayments = [];
        $this->missingSubmissions = [];

        // Check for draft submissions (excluding current month)
        $this->outstandingDrafts = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('status', 'draft')
            ->where(function($query) use ($currentMonth, $currentYear) {
                $query->where('year', '<', $currentYear)
                      ->orWhere(function($q) use ($currentMonth, $currentYear) {
                          $q->where('year', '=', $currentYear)
                            ->where('month', '<', $currentMonth);
                      });
            })
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Check for overdue payments (unpaid and past deadline, excluding current month)
        $this->overduePayments = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->whereNotIn('status', ['paid', 'draft'])
            ->where('payment_deadline', '<', now())
            ->where(function($query) use ($currentMonth, $currentYear) {
                $query->where('year', '<', $currentYear)
                      ->orWhere(function($q) use ($currentMonth, $currentYear) {
                          $q->where('year', '=', $currentYear)
                            ->where('month', '<', $currentMonth);
                      });
            })
            ->orderBy('payment_deadline', 'asc')
            ->get();

        // Check for missing submissions (excluding current month)
        // Check last 6 months for periods where workers existed but no submission was created
        for ($i = 1; $i <= 6; $i++) {
            $checkDate = $currentDate->copy()->subMonths($i);
            $month = $checkDate->month;
            $year = $checkDate->year;

            // Get active workers for that month
            $activeWorkerIds = \App\Models\ContractWorker::where('con_ctr_clab_no', $clabNo)
                ->where('con_end', '>=', $checkDate->startOfMonth()->toDateString())
                ->where('con_start', '<=', $checkDate->endOfMonth()->toDateString())
                ->pluck('con_wkr_id')
                ->unique();

            if ($activeWorkerIds->isEmpty()) {
                continue; // No workers that month, skip
            }

            // Check if ANY submission exists for that period (draft or finalized)
            $anySubmission = PayrollSubmission::where('contractor_clab_no', $clabNo)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            // If no submission exists at all, this is a missing submission
            if (!$anySubmission) {
                $this->missingSubmissions[] = (object)[
                    'month' => $month,
                    'year' => $year,
                    'month_year' => $checkDate->format('F Y'),
                    'total_workers' => $activeWorkerIds->count(),
                ];
            }
        }

        // Determine if blocked
        if ($this->outstandingDrafts->count() > 0) {
            $this->isBlocked = true;
            $this->blockReasons[] = [
                'type' => 'draft',
                'message' => 'You have ' . $this->outstandingDrafts->count() . ' unfinalized draft ' . \Str::plural('submission', $this->outstandingDrafts->count()) . ' from previous months that must be completed or deleted before submitting new payroll.',
            ];
        }

        if ($this->overduePayments->count() > 0) {
            $this->isBlocked = true;
            $this->blockReasons[] = [
                'type' => 'overdue',
                'message' => 'You have ' . $this->overduePayments->count() . ' overdue ' . \Str::plural('payment', $this->overduePayments->count()) . ' from previous months that must be paid before submitting new payroll.',
            ];
        }

        if (count($this->missingSubmissions) > 0) {
            $this->isBlocked = true;
            $this->blockReasons[] = [
                'type' => 'missing',
                'message' => 'You have ' . count($this->missingSubmissions) . ' missing payroll ' . \Str::plural('submission', count($this->missingSubmissions)) . ' from previous months that must be submitted before submitting new payroll.',
            ];
        }
    }

    public function render()
    {
        return view('livewire.client.timesheet')->layout('components.layouts.app', ['title' => __('Timesheet Management')]);
    }
}
