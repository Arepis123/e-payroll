<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\PayrollService;
use App\Services\ContractWorkerService;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    protected PayrollService $payrollService;
    protected ContractWorkerService $contractWorkerService;

    public function __construct(PayrollService $payrollService, ContractWorkerService $contractWorkerService)
    {
        $this->payrollService = $payrollService;
        $this->contractWorkerService = $contractWorkerService;
    }

    /**
     * Display the timesheet management page
     */
    public function index(Request $request)
    {
        $clabNo = $request->user()->contractor_clab_no;

        if (!$clabNo) {
            return view('client.timesheet', [
                'error' => 'No contractor CLAB number assigned to your account. Please contact administrator.',
            ]);
        }

        // Get current payroll period info
        $period = $this->payrollService->getCurrentPayrollPeriod();

        // Get active contracted workers only (no inactive workers for timesheet)
        $workers = $this->contractWorkerService->getActiveContractedWorkers($clabNo);

        // Get ALL submissions for this month to find all submitted workers
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $allSubmissionsThisMonth = \App\Models\PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->with('workers')
            ->get();

        // Get IDs of workers already submitted in ANY submission this month
        $submittedWorkerIds = $allSubmissionsThisMonth->flatMap(function($submission) {
            return $submission->workers->pluck('worker_id');
        })->unique()->toArray();

        // Filter out workers who have already been submitted
        $remainingWorkers = $workers->filter(function($worker) use ($submittedWorkerIds) {
            return !in_array($worker->wkr_id, $submittedWorkerIds);
        });

        // Determine current submission status based on what exists this month
        // If all workers submitted and all are pending_payment or paid, show that status
        // If there's a draft, show draft
        // Otherwise show draft (default for new submissions)
        $currentStatus = 'draft';
        if ($allSubmissionsThisMonth->count() > 0) {
            // Check if there's any draft
            $hasDraft = $allSubmissionsThisMonth->contains('status', 'draft');
            if ($hasDraft) {
                $currentStatus = 'draft';
            } else {
                // All submissions are non-draft, show most relevant status
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

        // Create a simple object for period info (don't create actual submission)
        $currentSubmission = (object)[
            'month' => $currentMonth,
            'year' => $currentYear,
            'status' => $currentStatus,
            'workers' => collect([]),
        ];

        // Prepare remaining workers for the form
        $workersData = $remainingWorkers->map(function($worker) {
            return (object)[
                'worker_id' => $worker->wkr_id,
                'worker_name' => $worker->name,
                'worker_passport' => $worker->ic_number,
                'basic_salary' => $worker->basic_salary ?? 1700,
                'ot_normal_hours' => 0,
                'ot_rest_hours' => 0,
                'ot_public_hours' => 0,
            ];
        });

        // Update penalties for any overdue submissions
        $this->payrollService->updateOverduePenalties($clabNo);

        // Get recent submissions history
        $recentSubmissions = $this->payrollService->getContractorSubmissions($clabNo)->take(5);

        // Get statistics (pass the count of remaining/unsubmitted workers)
        $stats = $this->payrollService->getContractorStatistics($clabNo, $remainingWorkers->count());

        return view('client.timesheet', compact(
            'period',
            'currentSubmission',
            'workers',
            'workersData',
            'recentSubmissions',
            'stats'
        ));
    }

    /**
     * Save timesheet submission
     */
    public function store(Request $request)
    {
        $clabNo = $request->user()->contractor_clab_no;

        if (!$clabNo) {
            return back()->with('error', 'No contractor CLAB number assigned.');
        }

        $validated = $request->validate([
            'action' => 'required|in:draft,submit',
            'draft_id' => 'nullable|exists:payroll_submissions,id',
            'workers' => 'required|array',
            'workers.*.included' => 'nullable|boolean',
            'workers.*.worker_id' => 'required|string',
            'workers.*.worker_name' => 'required|string',
            'workers.*.worker_passport' => 'required|string',
            'workers.*.basic_salary' => 'required|numeric|min:1700',
            'workers.*.ot_normal_hours' => 'nullable|numeric|min:0',
            'workers.*.ot_rest_hours' => 'nullable|numeric|min:0',
            'workers.*.ot_public_hours' => 'nullable|numeric|min:0',
        ]);

        // Filter only included workers
        $selectedWorkers = array_filter($validated['workers'], function($worker) {
            return isset($worker['included']) && $worker['included'];
        });

        // Check if at least one worker is selected
        if (empty($selectedWorkers)) {
            return back()->with('error', 'Please select at least one worker to submit payroll.');
        }

        $action = $validated['action'];
        $draftId = $validated['draft_id'] ?? null;

        try {
            if ($draftId) {
                // Updating existing draft
                $submission = \App\Models\PayrollSubmission::where('id', $draftId)
                    ->where('contractor_clab_no', $clabNo)
                    ->where('status', 'draft')
                    ->firstOrFail();

                // Delete existing workers from draft
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

                foreach ($selectedWorkers as $workerData) {
                    $payrollWorker = new \App\Models\PayrollWorker($workerData);
                    $payrollWorker->payroll_submission_id = $submission->id;
                    $previousMonthOt = $previousMonthOtMap[$workerData['worker_id']] ?? 0;
                    $payrollWorker->calculateSalary($previousMonthOt);
                    $payrollWorker->save();
                    $totalAmount += $payrollWorker->total_payment;
                }

                $submission->update([
                    'total_workers' => count($selectedWorkers),
                    'total_amount' => $totalAmount,
                    'total_with_penalty' => $totalAmount,
                ]);

                if ($action === 'submit') {
                    // Convert draft to pending_payment
                    $submission->update([
                        'status' => 'pending_payment',
                        'submitted_at' => now(),
                    ]);
                    $workerCount = count($selectedWorkers);
                    return redirect()->route('client.timesheet')
                        ->with('success', "Draft submitted successfully for {$submission->month_year}. {$workerCount} worker(s) included. Total amount: RM " . number_format($submission->total_amount, 2));
                } else {
                    // Keep as draft
                    $workerCount = count($selectedWorkers);
                    return redirect()->route('client.timesheet')
                        ->with('success', "Draft updated successfully. {$workerCount} worker(s) included.");
                }
            } else {
                // Creating new submission
                if ($action === 'draft') {
                    // Save as draft
                    $submission = $this->payrollService->savePayrollDraft($clabNo, $selectedWorkers);
                    $workerCount = count($selectedWorkers);
                    return redirect()->route('client.timesheet')
                        ->with('success', "Draft saved successfully. {$workerCount} worker(s) included.");
                } else {
                    // Submit for payment
                    $submission = $this->payrollService->savePayrollSubmission($clabNo, $selectedWorkers);
                    $workerCount = count($selectedWorkers);
                    return redirect()->route('client.timesheet')
                        ->with('success', "Timesheet submitted successfully for {$submission->month_year}. {$workerCount} worker(s) included. Total amount: RM " . number_format($submission->total_amount, 2));
                }
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to save timesheet: ' . $e->getMessage());
        }
    }

    /**
     * View specific payroll submission
     */
    public function show(Request $request, $id)
    {
        $clabNo = $request->user()->contractor_clab_no;

        $submission = \App\Models\PayrollSubmission::with(['workers', 'payment'])
            ->where('id', $id)
            ->where('contractor_clab_no', $clabNo)
            ->firstOrFail();

        return view('client.timesheet-detail', compact('submission'));
    }

    /**
     * Edit a draft submission
     */
    public function edit(Request $request, $id)
    {
        $clabNo = $request->user()->contractor_clab_no;

        // Load the draft submission
        $submission = \App\Models\PayrollSubmission::with('workers')
            ->where('id', $id)
            ->where('contractor_clab_no', $clabNo)
            ->where('status', 'draft')
            ->firstOrFail();

        // Get current payroll period info
        $period = $this->payrollService->getCurrentPayrollPeriod();

        // Get active contracted workers (for reference)
        $workers = $this->contractWorkerService->getActiveContractedWorkers($clabNo);

        // Only show workers that are in this draft batch
        $workersData = $submission->workers->map(function($draftWorker) use ($workers) {
            // Get worker details from the workers collection
            $worker = $workers->firstWhere('wkr_id', $draftWorker->worker_id);

            return (object)[
                'worker_id' => $draftWorker->worker_id,
                'worker_name' => $draftWorker->worker_name,
                'worker_passport' => $draftWorker->worker_passport,
                'basic_salary' => $draftWorker->basic_salary,
                'ot_normal_hours' => $draftWorker->ot_normal_hours,
                'ot_rest_hours' => $draftWorker->ot_rest_hours,
                'ot_public_hours' => $draftWorker->ot_public_hours,
                'included' => true, // All workers in draft are included
            ];
        });

        // Update penalties for any overdue submissions
        $this->payrollService->updateOverduePenalties($clabNo);

        // Get recent submissions history
        $recentSubmissions = $this->payrollService->getContractorSubmissions($clabNo)->take(5);

        // Calculate unsubmitted workers count for edit page
        // Get all active workers and subtract those in ANY submission this month
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $allActiveWorkers = $this->contractWorkerService->getActiveContractedWorkers($clabNo);
        $allSubmissionsThisMonth = \App\Models\PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->with('workers')
            ->get();
        $submittedWorkerIds = $allSubmissionsThisMonth->flatMap(function($sub) {
            return $sub->workers->pluck('worker_id');
        })->unique()->toArray();
        $unsubmittedCount = $allActiveWorkers->filter(function($worker) use ($submittedWorkerIds) {
            return !in_array($worker->wkr_id, $submittedWorkerIds);
        })->count();

        // Get statistics (pass the count of unsubmitted workers)
        $stats = $this->payrollService->getContractorStatistics($clabNo, $unsubmittedCount);

        // Set current submission
        $currentSubmission = $submission;

        return view('client.timesheet-edit', compact(
            'period',
            'currentSubmission',
            'submission',
            'workers',
            'workersData',
            'recentSubmissions',
            'stats'
        ));
    }

    /**
     * Submit a draft for payment
     */
    public function submitDraft(Request $request, $id)
    {
        $clabNo = $request->user()->contractor_clab_no;

        // Load the draft submission
        $submission = \App\Models\PayrollSubmission::where('id', $id)
            ->where('contractor_clab_no', $clabNo)
            ->where('status', 'draft')
            ->firstOrFail();

        try {
            // Update status to pending_payment and set submitted_at
            $submission->update([
                'status' => 'pending_payment',
                'submitted_at' => now(),
            ]);

            return redirect()->route('client.timesheet')
                ->with('success', "Draft submitted successfully for {$submission->month_year}. Total amount: RM " . number_format($submission->total_amount, 2));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit draft: ' . $e->getMessage());
        }
    }
}
