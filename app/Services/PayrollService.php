<?php

namespace App\Services;

use App\Models\PayrollSubmission;
use App\Models\PayrollWorker;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PayrollService
{
    /**
     * Get or create current month's payroll submission for a contractor
     */
    public function getCurrentMonthSubmission(string $clabNo): PayrollSubmission
    {
        $now = now();
        $month = $now->month;
        $year = $now->year;

        // Get last day of current month as deadline
        $deadline = Carbon::create($year, $month, 1)->endOfMonth();

        return PayrollSubmission::firstOrCreate(
            [
                'contractor_clab_no' => $clabNo,
                'month' => $month,
                'year' => $year,
            ],
            [
                'payment_deadline' => $deadline,
                'status' => 'draft',
            ]
        );
    }

    /**
     * Get payroll submission for specific month/year
     */
    public function getSubmissionForMonth(string $clabNo, int $month, int $year): ?PayrollSubmission
    {
        return PayrollSubmission::byContractor($clabNo)
            ->forMonth($month, $year)
            ->first();
    }

    /**
     * Create or update payroll submission with workers data
     *
     * IMPORTANT: OT payment is deferred to next month
     * - Current month OT is stored but not paid this month
     * - Previous month OT is included in this month's payment
     */
    public function savePayrollSubmission(string $clabNo, array $workersData): PayrollSubmission
    {
        $submission = $this->getCurrentMonthSubmission($clabNo);

        // Get previous month's submission to retrieve OT amounts
        $previousMonth = $submission->month - 1;
        $previousYear = $submission->year;

        // Handle year rollover (January gets December from previous year)
        if ($previousMonth < 1) {
            $previousMonth = 12;
            $previousYear--;
        }

        $previousSubmission = $this->getSubmissionForMonth($clabNo, $previousMonth, $previousYear);

        // Create a map of worker_id => previous month OT amount
        $previousMonthOtMap = [];
        if ($previousSubmission) {
            foreach ($previousSubmission->workers as $prevWorker) {
                $previousMonthOtMap[$prevWorker->worker_id] = $prevWorker->total_ot_pay;
            }
        }

        // Delete existing workers for this submission
        $submission->workers()->delete();

        $totalAmount = 0;

        // Create payroll workers and calculate totals
        foreach ($workersData as $workerData) {
            $payrollWorker = new PayrollWorker($workerData);
            $payrollWorker->payroll_submission_id = $submission->id;

            // Get previous month's OT for this worker (default to 0)
            $previousMonthOt = $previousMonthOtMap[$workerData['worker_id']] ?? 0;

            // Calculate salary with previous month's OT included in payment
            $payrollWorker->calculateSalary($previousMonthOt);
            $payrollWorker->save();

            $totalAmount += $payrollWorker->total_payment;
        }

        // Update submission totals
        $submission->update([
            'total_workers' => count($workersData),
            'total_amount' => $totalAmount,
            'total_with_penalty' => $totalAmount,
            'status' => 'pending_payment',
            'submitted_at' => now(),
        ]);

        return $submission->fresh(['workers']);
    }

    /**
     * Get all submissions for a contractor
     */
    public function getContractorSubmissions(string $clabNo): Collection
    {
        return PayrollSubmission::byContractor($clabNo)
            ->with(['workers', 'payment'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Check and update penalties for overdue submissions
     */
    public function updateOverduePenalties(string $clabNo): void
    {
        $overdueSubmissions = PayrollSubmission::byContractor($clabNo)
            ->overdue()
            ->get();

        foreach ($overdueSubmissions as $submission) {
            $submission->updatePenalty();
        }
    }

    /**
     * Get payroll statistics for contractor
     */
    public function getContractorStatistics(string $clabNo): array
    {
        $submissions = PayrollSubmission::byContractor($clabNo)->get();

        return [
            'total_submissions' => $submissions->count(),
            'paid_submissions' => $submissions->where('status', 'paid')->count(),
            'pending_submissions' => $submissions->where('status', 'pending_payment')->count(),
            'overdue_submissions' => $submissions->where('status', 'overdue')->count(),
            'total_paid_amount' => $submissions->where('status', 'paid')->sum('total_with_penalty'),
            'total_pending_amount' => $submissions->whereIn('status', ['pending_payment', 'overdue'])->sum('total_with_penalty'),
        ];
    }

    /**
     * Calculate what the current month's payroll would be based on contracted workers
     */
    public function calculateEstimatedPayroll(string $clabNo, ContractWorkerService $contractWorkerService): array
    {
        $workers = $contractWorkerService->getContractedWorkers($clabNo);

        $estimatedTotal = 0;
        $workerEstimates = [];

        foreach ($workers as $worker) {
            $basicSalary = $worker->basic_salary ?? 1700;

            // Estimate without overtime (minimum payment)
            $grossSalary = $basicSalary;
            $epfEmployee = $grossSalary * 0.02;
            $socsoEmployee = $grossSalary * 0.005;
            $totalDeductions = $epfEmployee + $socsoEmployee;
            $netSalary = $grossSalary - $totalDeductions;

            $epfEmployer = $grossSalary * 0.02;
            $socsoEmployer = $grossSalary * 0.0175;
            $totalEmployerContribution = $epfEmployer + $socsoEmployer;

            $totalPayment = $netSalary + $totalEmployerContribution;

            $workerEstimates[] = [
                'worker_id' => $worker->wkr_id,
                'worker_name' => $worker->name,
                'basic_salary' => $basicSalary,
                'estimated_payment' => $totalPayment,
            ];

            $estimatedTotal += $totalPayment;
        }

        return [
            'total_workers' => $workers->count(),
            'estimated_total' => $estimatedTotal,
            'workers' => $workerEstimates,
        ];
    }

    /**
     * Get current month and year for payroll
     */
    public function getCurrentPayrollPeriod(): array
    {
        $now = now();
        return [
            'month' => $now->month,
            'year' => $now->year,
            'month_name' => $now->format('F'),
            'deadline' => Carbon::create($now->year, $now->month, 1)->endOfMonth(),
            'days_until_deadline' => now()->diffInDays(Carbon::create($now->year, $now->month, 1)->endOfMonth(), false),
        ];
    }
}
