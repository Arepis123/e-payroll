<?php

namespace App\Models;

use App\Services\PaymentCalculatorService;
use Illuminate\Database\Eloquent\Model;

class PayrollWorker extends Model
{
    protected $fillable = [
        'payroll_submission_id',
        'worker_id',
        'worker_name',
        'worker_passport',
        'basic_salary',
        'regular_hours',
        'ot_normal_hours',
        'ot_rest_hours',
        'ot_public_hours',
        'regular_pay',
        'ot_normal_pay',
        'ot_rest_pay',
        'ot_public_pay',
        'total_ot_pay',
        'gross_salary',
        'advance_payment',
        'advance_payment_remarks',
        'deduction',
        'deduction_remarks',
        'epf_employee',
        'socso_employee',
        'total_deductions',
        'epf_employer',
        'socso_employer',
        'total_employer_contribution',
        'net_salary',
        'total_payment',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'regular_hours' => 'decimal:2',
        'ot_normal_hours' => 'decimal:2',
        'ot_rest_hours' => 'decimal:2',
        'ot_public_hours' => 'decimal:2',
        'regular_pay' => 'decimal:2',
        'ot_normal_pay' => 'decimal:2',
        'ot_rest_pay' => 'decimal:2',
        'ot_public_pay' => 'decimal:2',
        'total_ot_pay' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'advance_payment' => 'decimal:2',
        'deduction' => 'decimal:2',
        'epf_employee' => 'decimal:2',
        'socso_employee' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'epf_employer' => 'decimal:2',
        'socso_employer' => 'decimal:2',
        'total_employer_contribution' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'total_payment' => 'decimal:2',
    ];

    /**
     * Get the payroll submission this worker belongs to
     */
    public function payrollSubmission()
    {
        return $this->belongsTo(PayrollSubmission::class);
    }

    /**
     * Alias for payrollSubmission relationship
     */
    public function submission()
    {
        return $this->payrollSubmission();
    }

    /**
     * Get the worker details from the worker_db database
     */
    public function worker()
    {
        return $this->belongsTo(Worker::class, 'worker_id', 'wkr_id');
    }

    /**
     * Get all transactions for this payroll worker
     */
    public function transactions()
    {
        return $this->hasMany(PayrollWorkerTransaction::class);
    }

    /**
     * Get total advance payment from all transactions
     */
    public function getTotalAdvancePaymentAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'advance_payment')
            ->sum('amount') ?? 0;
    }

    /**
     * Get total deductions from all transactions
     */
    public function getTotalDeductionAttribute(): float
    {
        return $this->transactions()
            ->where('type', 'deduction')
            ->sum('amount') ?? 0;
    }

    /**
     * Calculate all salary components using PaymentCalculatorService
     *
     * IMPORTANT: OT Payment Deferral
     * - Current month OT is CALCULATED and STORED but NOT PAID this month
     * - Previous month OT is INCLUDED in this month's payment
     *
     * This system collects: Basic Salary + Employer Contributions (EPF + SOCSO) + Previous Month OT
     * Worker receives: Basic Salary - Worker Deductions (EPF + SOCSO) + Previous Month OT
     *
     * Formula (from FORMULA PENGIRAAN GAJI DAN OVERTIME.csv):
     * - Basic Salary: RM 1,700 minimum
     * - EPF Worker: 2% | EPF Employer: 2%
     * - SOCSO Worker: 0.5% | SOCSO Employer: 1.75%
     * - Daily Rate: Basic / 26 days
     * - Hourly Rate: Daily / 8 hours
     * - Weekday OT: Hourly × 1.5
     * - Rest Day OT: Hourly × 2.0
     * - Public Holiday OT: Hourly × 3.0
     *
     * @param float $previousMonthOtPay Previous month's OT amount to be paid this month
     */
    public function calculateSalary(float $previousMonthOtPay = 0): void
    {
        $calculator = app(PaymentCalculatorService::class);

        // Calculate CURRENT month overtime (to be paid NEXT month)
        $this->ot_normal_pay = round($calculator->calculateWeekdayOTRate($this->basic_salary) * $this->ot_normal_hours, 2);
        $this->ot_rest_pay = round($calculator->calculateRestDayOTRate($this->basic_salary) * $this->ot_rest_hours, 2);
        $this->ot_public_pay = round($calculator->calculatePublicHolidayOTRate($this->basic_salary) * $this->ot_public_hours, 2);
        $this->total_ot_pay = $this->ot_normal_pay + $this->ot_rest_pay + $this->ot_public_pay;

        // Regular pay is the basic salary
        $this->regular_pay = $this->basic_salary;

        // Gross salary = Basic + PREVIOUS month's OT + Advance Payment - Deduction
        // Formula from CSV: Jumlah Gaji = Basic + Advance Payment - Deduction
        // Use transaction totals if available, otherwise use direct fields
        $totalAdvancePayment = $this->exists ? $this->total_advance_payment : ($this->advance_payment ?? 0);
        $totalDeduction = $this->exists ? $this->total_deduction : ($this->deduction ?? 0);

        $this->gross_salary = $this->basic_salary + $previousMonthOtPay + $totalAdvancePayment - $totalDeduction;

        // Employee deductions (calculated on gross salary including previous month OT)
        $this->epf_employee = $calculator->calculateWorkerEPF($this->gross_salary);
        $this->socso_employee = $calculator->calculateWorkerSOCSO($this->gross_salary);
        $this->total_deductions = $this->epf_employee + $this->socso_employee;

        // Employer contributions (calculated on gross salary including previous month OT)
        $this->epf_employer = $calculator->calculateEmployerEPF($this->gross_salary);
        $this->socso_employer = $calculator->calculateEmployerSOCSO($this->gross_salary);
        $this->total_employer_contribution = $this->epf_employer + $this->socso_employer;

        // Final amounts
        // Net salary = What worker receives (Gross - Worker deductions)
        $this->net_salary = $this->gross_salary - $this->total_deductions;

        // Total payment = What system collects from contractor (Gross + Employer contributions)
        $this->total_payment = $this->gross_salary + $this->total_employer_contribution;
    }

    /**
     * Get total overtime hours
     */
    public function getTotalOvertimeHoursAttribute(): float
    {
        return $this->ot_normal_hours + $this->ot_rest_hours + $this->ot_public_hours;
    }

    /**
     * Get payment breakdown using calculator service
     */
    public function getPaymentBreakdown(): array
    {
        $calculator = app(PaymentCalculatorService::class);

        return $calculator->calculateWorkerPayment(
            $this->basic_salary,
            $this->ot_normal_hours,
            $this->ot_rest_hours,
            $this->ot_public_hours,
            $this->advance_payment ?? 0,
            $this->deduction ?? 0
        );
    }
}
