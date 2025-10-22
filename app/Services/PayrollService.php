<?php

namespace App\Services;

use App\Models\PayrollSubmission;
use App\Models\PayrollWorker;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PayrollService
{
    /**
     * Get current month's latest draft payroll submission for a contractor
     * Returns the latest draft submission or null (does NOT create)
     * Note: This method is deprecated - use direct queries instead
     */
    public function getCurrentMonthSubmission(string $clabNo): ?PayrollSubmission
    {
        $now = now();
        $month = $now->month;
        $year = $now->year;

        // Find the latest draft submission for this month
        return PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'draft')
            ->latest('created_at')
            ->first();
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
     * Save payroll as DRAFT (not submitted for payment yet)
     * Draft submissions can be edited later before final submission
     */
    public function savePayrollDraft(string $clabNo, array $workersData): PayrollSubmission
    {
        // Create a NEW draft submission
        $now = now();
        $month = $now->month;
        $year = $now->year;
        $deadline = Carbon::create($year, $month, 1)->endOfMonth();

        $submission = PayrollSubmission::create([
            'contractor_clab_no' => $clabNo,
            'month' => $month,
            'year' => $year,
            'payment_deadline' => $deadline,
            'status' => 'draft',
            'submitted_at' => null,  // Not submitted yet
        ]);

        // Get previous month's submission to retrieve OT amounts
        $previousMonth = $month - 1;
        $previousYear = $year;

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

        $totalAmount = 0;

        // Create payroll workers and calculate totals
        foreach ($workersData as $workerData) {
            $payrollWorker = new PayrollWorker($workerData);
            $payrollWorker->payroll_submission_id = $submission->id;

            // Get previous month's OT for this worker (default to 0)
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

            // Total amount is what the system collects (Gross + Employer contributions)
            $totalAmount += $payrollWorker->total_payment;
        }

        // Update submission totals
        $submission->update([
            'total_workers' => count($workersData),
            'total_amount' => $totalAmount,
            'total_with_penalty' => $totalAmount,
        ]);

        return $submission->fresh(['workers']);
    }

    /**
     * Create or update payroll submission with workers data
     *
     * Payment Calculation (based on FORMULA PENGIRAAN GAJI DAN OVERTIME.csv):
     * - System collects: Basic Salary + Employer Contributions (EPF + SOCSO) + PREVIOUS Month OT
     * - Worker receives: Basic Salary - Worker Deductions (EPF + SOCSO) + PREVIOUS Month OT
     *
     * IMPORTANT: OT Payment Deferral
     * - Current month OT is CALCULATED and STORED but NOT PAID this month
     * - Previous month OT is INCLUDED in this month's payment
     * - This ensures OT is verified before payment
     */
    public function savePayrollSubmission(string $clabNo, array $workersData): PayrollSubmission
    {
        // Create a NEW submission for this batch of workers
        $now = now();
        $month = $now->month;
        $year = $now->year;
        $deadline = Carbon::create($year, $month, 1)->endOfMonth();

        $submission = PayrollSubmission::create([
            'contractor_clab_no' => $clabNo,
            'month' => $month,
            'year' => $year,
            'payment_deadline' => $deadline,
            'status' => 'pending_payment',
            'submitted_at' => now(),
        ]);

        // Get previous month's submission to retrieve OT amounts
        $previousMonth = $month - 1;
        $previousYear = $year;

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

        $totalAmount = 0;

        // Create payroll workers and calculate totals
        foreach ($workersData as $workerData) {
            $payrollWorker = new PayrollWorker($workerData);
            $payrollWorker->payroll_submission_id = $submission->id;

            // Get previous month's OT for this worker (default to 0)
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

            // Total amount is what the system collects (Gross + Employer contributions)
            $totalAmount += $payrollWorker->total_payment;
        }

        // Update submission totals
        $submission->update([
            'total_workers' => count($workersData),
            'total_amount' => $totalAmount,
            'total_with_penalty' => $totalAmount,
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
    public function getContractorStatistics(string $clabNo, ?int $unsubmittedWorkersCount = null): array
    {
        $submissions = PayrollSubmission::byContractor($clabNo)->get();

        return [
            'total_submissions' => $submissions->count(),
            'paid_submissions' => $submissions->where('status', 'paid')->count(),
            'pending_submissions' => $submissions->where('status', 'pending_payment')->count(),
            'overdue_submissions' => $submissions->where('status', 'overdue')->count(),
            'total_paid_amount' => $submissions->where('status', 'paid')->sum('total_with_penalty'),
            'total_pending_amount' => $submissions->whereIn('status', ['pending_payment', 'overdue'])->sum('total_with_penalty'),
            'unsubmitted_workers' => $unsubmittedWorkersCount ?? 0,
        ];
    }

    /**
     * Calculate what the current month's payroll would be based on contracted workers
     */
    public function calculateEstimatedPayroll(string $clabNo, ContractWorkerService $contractWorkerService): array
    {
        $workers = $contractWorkerService->getContractedWorkers($clabNo);
        $calculator = app(PaymentCalculatorService::class);

        $estimatedTotal = 0;
        $workerEstimates = [];

        foreach ($workers as $worker) {
            $basicSalary = $worker->basic_salary ?? 1700;

            // Calculate total payment to CLAB (Basic + Employer contributions)
            $totalPayment = $calculator->calculateTotalPaymentToCLAB($basicSalary);

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
        $deadline = Carbon::create($now->year, $now->month, 1)->endOfMonth();

        return [
            'month' => $now->month,
            'year' => $now->year,
            'month_name' => $now->format('F'),
            'deadline' => $deadline,
            'days_until_deadline' => (int) now()->diffInDays($deadline, false),
        ];
    }
}
