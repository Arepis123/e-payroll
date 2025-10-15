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

        // Get current month's submission (if exists)
        $currentSubmission = $this->payrollService->getCurrentMonthSubmission($clabNo);

        // Get contracted workers
        $workers = $this->contractWorkerService->getContractedWorkers($clabNo);

        // If submission has workers, use them; otherwise prepare empty data for workers
        if ($currentSubmission->workers->count() > 0) {
            $workersData = $currentSubmission->workers;
        } else {
            // Prepare workers for initial form
            $workersData = $workers->map(function($worker) {
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
        }

        // Update penalties for any overdue submissions
        $this->payrollService->updateOverduePenalties($clabNo);

        // Get recent submissions history
        $recentSubmissions = $this->payrollService->getContractorSubmissions($clabNo)->take(5);

        // Get statistics
        $stats = $this->payrollService->getContractorStatistics($clabNo);

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
            'workers' => 'required|array',
            'workers.*.worker_id' => 'required|string',
            'workers.*.worker_name' => 'required|string',
            'workers.*.worker_passport' => 'required|string',
            'workers.*.basic_salary' => 'required|numeric|min:1700',
            'workers.*.ot_normal_hours' => 'nullable|numeric|min:0',
            'workers.*.ot_rest_hours' => 'nullable|numeric|min:0',
            'workers.*.ot_public_hours' => 'nullable|numeric|min:0',
        ]);

        try {
            $submission = $this->payrollService->savePayrollSubmission($clabNo, $validated['workers']);

            return redirect()->route('client.timesheet')
                ->with('success', "Timesheet submitted successfully for {$submission->month_year}. Total amount: RM " . number_format($submission->total_amount, 2));
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit timesheet: ' . $e->getMessage());
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
}
