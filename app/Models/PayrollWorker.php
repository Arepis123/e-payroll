<?php

namespace App\Models;

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
     * Calculate all salary components based on hours and basic salary
     * Based on formula: Daily Rate = Basic Salary / 26, Hourly Rate = Daily Rate / 8
     *
     * IMPORTANT: OT is paid in the following month
     * - Current month OT is calculated and stored but NOT paid this month
     * - Previous month OT is included in this month's payment
     *
     * @param float $previousMonthOtPay Previous month's OT amount to be paid this month
     */
    public function calculateSalary(float $previousMonthOtPay = 0): void
    {
        // Calculate hourly rate for current month OT (to be paid next month)
        $dailyRate = $this->basic_salary / 26;
        $hourlyRate = $dailyRate / 8;

        // Regular pay (basic salary for current month)
        $this->regular_pay = $this->basic_salary;

        // Current month OT calculations (stored but NOT paid this month)
        $this->ot_normal_pay = $this->ot_normal_hours * ($hourlyRate * 1.5); // Normal day OT: 1.5x
        $this->ot_rest_pay = $this->ot_rest_hours * ($hourlyRate * 2); // Rest day OT: 2x
        $this->ot_public_pay = $this->ot_public_hours * ($hourlyRate * 3); // Public holiday OT: 3x
        $this->total_ot_pay = $this->ot_normal_pay + $this->ot_rest_pay + $this->ot_public_pay;

        // Gross salary = Basic salary + Previous month's OT
        // NOTE: Current month OT is NOT included in payment calculation
        $this->gross_salary = $this->basic_salary + $previousMonthOtPay;

        // Employee deductions (calculated on gross salary including previous month OT)
        $this->epf_employee = $this->gross_salary * 0.02; // 2% EPF
        $this->socso_employee = $this->gross_salary * 0.005; // 0.5% SOCSO
        $this->total_deductions = $this->epf_employee + $this->socso_employee;

        // Employer contributions (calculated on gross salary including previous month OT)
        $this->epf_employer = $this->gross_salary * 0.02; // 2% EPF
        $this->socso_employer = $this->gross_salary * 0.0175; // 1.75% SOCSO
        $this->total_employer_contribution = $this->epf_employer + $this->socso_employer;

        // Final amounts
        $this->net_salary = $this->gross_salary - $this->total_deductions;
        $this->total_payment = $this->net_salary + $this->total_employer_contribution;
    }

    /**
     * Get total overtime hours
     */
    public function getTotalOvertimeHoursAttribute(): float
    {
        return $this->ot_normal_hours + $this->ot_rest_hours + $this->ot_public_hours;
    }
}
