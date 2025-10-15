<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PayrollSubmission extends Model
{
    protected $fillable = [
        'contractor_clab_no',
        'month',
        'year',
        'payment_deadline',
        'status',
        'has_penalty',
        'penalty_amount',
        'total_amount',
        'total_with_penalty',
        'total_workers',
        'submitted_at',
        'paid_at',
    ];

    protected $casts = [
        'payment_deadline' => 'date',
        'has_penalty' => 'boolean',
        'penalty_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_with_penalty' => 'decimal:2',
        'submitted_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the workers in this payroll submission
     */
    public function workers()
    {
        return $this->hasMany(PayrollWorker::class);
    }

    /**
     * Get the payment record for this submission
     */
    public function payment()
    {
        return $this->hasOne(PayrollPayment::class);
    }

    /**
     * Check if payment deadline has passed
     */
    public function isOverdue(): bool
    {
        return $this->payment_deadline->isPast() && $this->status !== 'paid';
    }

    /**
     * Calculate penalty if overdue (8% of total amount)
     */
    public function calculatePenalty(): float
    {
        if ($this->isOverdue()) {
            return $this->total_amount * 0.08;
        }
        return 0;
    }

    /**
     * Update penalty if overdue
     */
    public function updatePenalty(): void
    {
        if ($this->isOverdue() && !$this->has_penalty) {
            $penalty = $this->calculatePenalty();
            $this->update([
                'has_penalty' => true,
                'penalty_amount' => $penalty,
                'total_with_penalty' => $this->total_amount + $penalty,
            ]);
        }
    }

    /**
     * Get days until deadline
     */
    public function daysUntilDeadline(): int
    {
        return now()->diffInDays($this->payment_deadline, false);
    }

    /**
     * Scope to filter by contractor
     */
    public function scopeByContractor($query, string $clabNo)
    {
        return $query->where('contractor_clab_no', $clabNo);
    }

    /**
     * Scope to filter by month and year
     */
    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Scope to get overdue submissions
     */
    public function scopeOverdue($query)
    {
        return $query->where('payment_deadline', '<', now())
                    ->whereNotIn('status', ['paid']);
    }

    /**
     * Get formatted month/year
     */
    public function getMonthYearAttribute(): string
    {
        return Carbon::create($this->year, $this->month, 1)->format('F Y');
    }
}
