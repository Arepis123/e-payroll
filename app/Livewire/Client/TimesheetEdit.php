<?php

namespace App\Livewire\Client;

use App\Models\PayrollSubmission;
use App\Services\PayrollService;
use App\Services\ContractWorkerService;
use Livewire\Component;

class TimesheetEdit extends Component
{
    protected PayrollService $payrollService;
    protected ContractWorkerService $contractWorkerService;

    public $submissionId;
    public $workers = [];
    public $selectedWorkers = [];
    public $period;
    public $currentSubmission;
    public $successMessage = '';
    public $errorMessage = '';

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

    public function mount($id)
    {
        $this->submissionId = $id;
        $this->loadData();
    }

    public function loadData()
    {
        $clabNo = auth()->user()->contractor_clab_no;

        if (!$clabNo) {
            $this->errorMessage = 'No contractor CLAB number assigned to your account. Please contact administrator.';
            return;
        }

        // Load the draft submission with transactions
        $submission = PayrollSubmission::with('workers.transactions')
            ->where('id', $this->submissionId)
            ->where('contractor_clab_no', $clabNo)
            ->where('status', 'draft')
            ->firstOrFail();

        $this->currentSubmission = $submission;

        // Get current payroll period info
        $this->period = $this->payrollService->getCurrentPayrollPeriod();

        // Prepare workers data with transactions
        $this->workers = $submission->workers->map(function($draftWorker, $index) {
            return [
                'index' => $index,
                'worker_id' => $draftWorker->worker_id,
                'worker_name' => $draftWorker->worker_name,
                'worker_passport' => $draftWorker->worker_passport,
                'basic_salary' => $draftWorker->basic_salary,
                'ot_normal_hours' => $draftWorker->ot_normal_hours,
                'ot_rest_hours' => $draftWorker->ot_rest_hours,
                'ot_public_hours' => $draftWorker->ot_public_hours,
                'transactions' => $draftWorker->transactions->map(function($txn) {
                    return [
                        'type' => $txn->type,
                        'amount' => $txn->amount,
                        'remarks' => $txn->remarks
                    ];
                })->toArray(),
                'included' => true,
            ];
        })->values()->toArray();

        // Initialize selected workers (all selected by default)
        $this->selectedWorkers = collect($this->workers)->pluck('worker_id')->toArray();
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

        // Update the worker's transactions array directly
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

        // Close modal
        $this->closeTransactionModal();
        $this->successMessage = "Transactions saved successfully for {$workerName}. Total: Advance RM " . number_format($totalAdvancePayment, 2) . ", Deduction RM " . number_format($totalDeduction, 2);
    }

    public function updateDraft()
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
            $this->errorMessage = 'Validation failed: ' . $e->getMessage();
            return;
        }

        // Filter only selected workers
        $selectedWorkersData = collect($this->workers)->filter(function($worker) {
            return in_array($worker['worker_id'], $this->selectedWorkers);
        })->toArray();

        if (empty($selectedWorkersData)) {
            $this->errorMessage = 'Please select at least one worker to submit payroll.';
            return;
        }

        try {
            // Update existing draft
            $submission = PayrollSubmission::where('id', $this->submissionId)
                ->where('contractor_clab_no', $clabNo)
                ->where('status', 'draft')
                ->firstOrFail();

            // Delete existing workers and transactions
            foreach ($submission->workers as $worker) {
                $worker->transactions()->delete();
            }
            $submission->workers()->delete();

            // Recalculate with new worker data
            $totalAmount = 0;
            $previousMonth = now()->month - 1;
            $previousYear = now()->year;
            if ($previousMonth < 1) {
                $previousMonth = 12;
                $previousYear--;
            }
            $previousSubmission = $this->payrollService->getSubmissionForMonth($clabNo, $previousMonth, $previousYear);
            $previousMonthOtMap = [];
            if ($previousSubmission) {
                foreach ($previousSubmission->workers as $prevWorker) {
                    $previousMonthOtMap[$prevWorker->worker_id] = $prevWorker->total_ot_pay;
                }
            }

            foreach ($selectedWorkersData as $workerData) {
                $payrollWorker = new \App\Models\PayrollWorker($workerData);
                $payrollWorker->payroll_submission_id = $submission->id;
                $previousMonthOt = $previousMonthOtMap[$workerData['worker_id']] ?? 0;

                // Save worker first (without final calculations)
                $payrollWorker->save();

                // Save transactions BEFORE calculating salary
                if (isset($workerData['transactions']) && is_array($workerData['transactions'])) {
                    foreach ($workerData['transactions'] as $transaction) {
                        $payrollWorker->transactions()->create([
                            'type' => $transaction['type'],
                            'amount' => $transaction['amount'],
                            'remarks' => $transaction['remarks'],
                        ]);
                    }
                }

                // NOW calculate salary with transactions in database
                $payrollWorker->calculateSalary($previousMonthOt);
                $payrollWorker->save();

                $totalAmount += $payrollWorker->total_payment;
            }

            // Calculate service charge, SST, and grand total
            $serviceCharge = count($selectedWorkersData) * 200; // RM200 per worker
            $sst = $serviceCharge * 0.08; // 8% SST on service charge
            $grandTotal = $totalAmount + $serviceCharge + $sst;

            $submission->update([
                'total_workers' => count($selectedWorkersData),
                'total_amount' => $totalAmount,
                'service_charge' => $serviceCharge,
                'sst' => $sst,
                'grand_total' => $grandTotal,
                'total_with_penalty' => $grandTotal,
            ]);

            if ($action === 'submit') {
                // Convert draft to pending_payment
                $submission->update([
                    'status' => 'pending_payment',
                    'submitted_at' => now(),
                ]);
                $workerCount = count($selectedWorkersData);
                return redirect()->route('timesheet')
                    ->with('success', "Draft submitted successfully for {$submission->month_year}. {$workerCount} worker(s) included. Total amount: RM " . number_format($submission->grand_total, 2));
            } else {
                // Keep as draft
                $workerCount = count($selectedWorkersData);
                $this->successMessage = "Draft updated successfully. {$workerCount} worker(s) included.";
                $this->loadData(); // Reload data
            }
        } catch (\Exception $e) {
            $this->errorMessage = 'Failed to save timesheet: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.client.timesheet-edit')->layout('components.layouts.app', ['title' => __('Edit Draft Submission')]);
    }
}
