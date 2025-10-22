<?php

namespace App\Services;

/**
 * Payment Calculator Service
 *
 * Handles all salary and overtime calculations for the e-payroll system.
 * Based on Malaysian labor regulations for foreign construction workers.
 *
 * Reference: FORMULA PENGIRAAN GAJI DAN OVERTIME.csv
 */
class PaymentCalculatorService
{
    // Constants based on Malaysian regulations
    public const MINIMUM_SALARY = 1700.00; // RM 1,700 minimum for foreign workers
    public const EPF_WORKER_RATE = 0.02;   // 2% EPF deduction from worker
    public const EPF_EMPLOYER_RATE = 0.02; // 2% EPF contribution by employer
    public const SOCSO_WORKER_RATE = 0.005; // 0.5% SOCSO deduction from worker
    public const SOCSO_EMPLOYER_RATE = 0.0175; // 1.75% SOCSO contribution by employer

    // Overtime calculation constants
    public const WORKING_DAYS_PER_MONTH = 26;
    public const WORKING_HOURS_PER_DAY = 8;
    public const OT_WEEKDAY_MULTIPLIER = 1.5;
    public const OT_REST_DAY_MULTIPLIER = 2.0;
    public const OT_PUBLIC_HOLIDAY_MULTIPLIER = 3.0;

    /**
     * Calculate worker's EPF deduction (2%)
     */
    public function calculateWorkerEPF(float $basicSalary): float
    {
        return round($basicSalary * self::EPF_WORKER_RATE, 2);
    }

    /**
     * Calculate employer's EPF contribution (2%)
     */
    public function calculateEmployerEPF(float $basicSalary): float
    {
        return round($basicSalary * self::EPF_EMPLOYER_RATE, 2);
    }

    /**
     * Calculate worker's SOCSO deduction (0.5%)
     */
    public function calculateWorkerSOCSO(float $basicSalary): float
    {
        return round($basicSalary * self::SOCSO_WORKER_RATE, 2);
    }

    /**
     * Calculate employer's SOCSO contribution (1.75%)
     */
    public function calculateEmployerSOCSO(float $basicSalary): float
    {
        return round($basicSalary * self::SOCSO_EMPLOYER_RATE, 2);
    }

    /**
     * Calculate total worker deductions (EPF + SOCSO)
     */
    public function calculateTotalWorkerDeductions(float $basicSalary): float
    {
        return $this->calculateWorkerEPF($basicSalary) + $this->calculateWorkerSOCSO($basicSalary);
    }

    /**
     * Calculate total employer contributions (EPF + SOCSO)
     */
    public function calculateTotalEmployerContributions(float $basicSalary): float
    {
        return $this->calculateEmployerEPF($basicSalary) + $this->calculateEmployerSOCSO($basicSalary);
    }

    /**
     * Calculate net salary (after deductions)
     * This is what the worker receives
     */
    public function calculateNetSalary(float $basicSalary): float
    {
        return $basicSalary - $this->calculateTotalWorkerDeductions($basicSalary);
    }

    /**
     * Calculate total amount employer must pay to CLAB
     * Formula: Basic Salary + Employer Contributions
     */
    public function calculateTotalPaymentToCLAB(float $basicSalary): float
    {
        return $basicSalary + $this->calculateTotalEmployerContributions($basicSalary);
    }

    /**
     * Calculate daily rate (ORP - Ordinary Rate of Pay per day)
     * Formula: Basic Salary / 26 working days
     */
    public function calculateDailyRate(float $basicSalary): float
    {
        return round($basicSalary / self::WORKING_DAYS_PER_MONTH, 2);
    }

    /**
     * Calculate hourly rate (HRP - Hourly Rate of Pay)
     * Formula: Daily Rate / 8 working hours
     */
    public function calculateHourlyRate(float $basicSalary): float
    {
        $dailyRate = $this->calculateDailyRate($basicSalary);
        return round($dailyRate / self::WORKING_HOURS_PER_DAY, 2);
    }

    /**
     * Calculate weekday overtime rate
     * Formula: Hourly Rate × 1.5
     */
    public function calculateWeekdayOTRate(float $basicSalary): float
    {
        $hourlyRate = $this->calculateHourlyRate($basicSalary);
        return round($hourlyRate * self::OT_WEEKDAY_MULTIPLIER, 2);
    }

    /**
     * Calculate rest day overtime rate
     * Formula: Hourly Rate × 2.0
     */
    public function calculateRestDayOTRate(float $basicSalary): float
    {
        $hourlyRate = $this->calculateHourlyRate($basicSalary);
        return round($hourlyRate * self::OT_REST_DAY_MULTIPLIER, 2);
    }

    /**
     * Calculate public holiday overtime rate
     * Formula: Hourly Rate × 3.0
     */
    public function calculatePublicHolidayOTRate(float $basicSalary): float
    {
        $hourlyRate = $this->calculateHourlyRate($basicSalary);
        return round($hourlyRate * self::OT_PUBLIC_HOLIDAY_MULTIPLIER, 2);
    }

    /**
     * Calculate total overtime pay
     *
     * @param float $basicSalary
     * @param float $weekdayOTHours Number of overtime hours on weekdays
     * @param float $restDayOTHours Number of overtime hours on rest days
     * @param float $publicHolidayOTHours Number of overtime hours on public holidays
     * @return float Total overtime amount
     */
    public function calculateTotalOvertimePay(
        float $basicSalary,
        float $weekdayOTHours = 0,
        float $restDayOTHours = 0,
        float $publicHolidayOTHours = 0
    ): float {
        $weekdayOT = $this->calculateWeekdayOTRate($basicSalary) * $weekdayOTHours;
        $restDayOT = $this->calculateRestDayOTRate($basicSalary) * $restDayOTHours;
        $publicHolidayOT = $this->calculatePublicHolidayOTRate($basicSalary) * $publicHolidayOTHours;

        return round($weekdayOT + $restDayOT + $publicHolidayOT, 2);
    }

    /**
     * Calculate complete worker payment breakdown
     *
     * @param float $basicSalary Worker's basic salary
     * @param float $weekdayOTHours Weekday overtime hours
     * @param float $restDayOTHours Rest day overtime hours
     * @param float $publicHolidayOTHours Public holiday overtime hours
     * @param float $advancePayment Advance payment to worker (loan)
     * @param float $deduction Deduction from worker's salary
     * @return array Complete payment breakdown
     */
    public function calculateWorkerPayment(
        float $basicSalary,
        float $weekdayOTHours = 0,
        float $restDayOTHours = 0,
        float $publicHolidayOTHours = 0,
        float $advancePayment = 0,
        float $deduction = 0
    ): array {
        // Basic salary calculations
        $workerEPF = $this->calculateWorkerEPF($basicSalary);
        $workerSOCSO = $this->calculateWorkerSOCSO($basicSalary);
        $employerEPF = $this->calculateEmployerEPF($basicSalary);
        $employerSOCSO = $this->calculateEmployerSOCSO($basicSalary);

        $totalWorkerDeductions = $workerEPF + $workerSOCSO;
        $totalEmployerContributions = $employerEPF + $employerSOCSO;

        $netBasicSalary = $basicSalary - $totalWorkerDeductions;

        // Overtime calculations
        $overtimePay = $this->calculateTotalOvertimePay(
            $basicSalary,
            $weekdayOTHours,
            $restDayOTHours,
            $publicHolidayOTHours
        );

        // Total calculations
        // Formula: Jumlah Gaji = Basic + OT + Advance Payment - Deduction
        $totalGrossPay = $basicSalary + $overtimePay + $advancePayment - $deduction;
        $totalNetPay = $netBasicSalary + $overtimePay + $advancePayment - $deduction;
        $totalPaymentToCLAB = $basicSalary + $totalEmployerContributions + $overtimePay;

        return [
            // Basic salary components
            'basic_salary' => $basicSalary,
            'worker_epf' => $workerEPF,
            'worker_socso' => $workerSOCSO,
            'employer_epf' => $employerEPF,
            'employer_socso' => $employerSOCSO,
            'total_worker_deductions' => $totalWorkerDeductions,
            'total_employer_contributions' => $totalEmployerContributions,
            'net_basic_salary' => $netBasicSalary,

            // Overtime components
            'weekday_ot_hours' => $weekdayOTHours,
            'rest_day_ot_hours' => $restDayOTHours,
            'public_holiday_ot_hours' => $publicHolidayOTHours,
            'weekday_ot_rate' => $this->calculateWeekdayOTRate($basicSalary),
            'rest_day_ot_rate' => $this->calculateRestDayOTRate($basicSalary),
            'public_holiday_ot_rate' => $this->calculatePublicHolidayOTRate($basicSalary),
            'total_overtime_pay' => $overtimePay,

            // Additional adjustments
            'advance_payment' => $advancePayment,
            'deduction' => $deduction,

            // Totals
            'total_gross_pay' => $totalGrossPay,
            'total_net_pay' => $totalNetPay,
            'total_payment_to_clab' => $totalPaymentToCLAB,

            // Rate information
            'hourly_rate' => $this->calculateHourlyRate($basicSalary),
            'daily_rate' => $this->calculateDailyRate($basicSalary),
        ];
    }

    /**
     * Get payment summary for display
     */
    public function getPaymentSummary(array $paymentBreakdown): array
    {
        return [
            'Basic Salary' => 'RM ' . number_format($paymentBreakdown['basic_salary'], 2),
            'Worker Deductions (EPF + SOCSO)' => 'RM ' . number_format($paymentBreakdown['total_worker_deductions'], 2),
            'Net Basic Salary' => 'RM ' . number_format($paymentBreakdown['net_basic_salary'], 2),
            'Overtime Pay' => 'RM ' . number_format($paymentBreakdown['total_overtime_pay'], 2),
            'Advance Payment' => 'RM ' . number_format($paymentBreakdown['advance_payment'], 2),
            'Deduction' => 'RM ' . number_format($paymentBreakdown['deduction'], 2),
            'Total Net Pay (Worker Receives)' => 'RM ' . number_format($paymentBreakdown['total_net_pay'], 2),
            'Employer Contributions' => 'RM ' . number_format($paymentBreakdown['total_employer_contributions'], 2),
            'Total Payment to CLAB' => 'RM ' . number_format($paymentBreakdown['total_payment_to_clab'], 2),
        ];
    }
}
