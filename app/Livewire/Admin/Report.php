<?php

namespace App\Livewire\Admin;

use App\Models\PayrollSubmission;
use App\Models\PayrollPayment;
use App\Models\PayrollWorker;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Report extends Component
{
    public $reportType = '';
    public $period = '';
    public $clientFilter = '';
    public $stats = [];
    public $clientPayments = [];
    public $topWorkers = [];
    public $chartData = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadClientPayments();
        $this->loadTopWorkers();
        $this->loadChartData();
    }

    protected function loadStats()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Total paid this month
        $totalPaid = PayrollPayment::where('status', 'completed')
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->sum('amount');

        // Pending amount
        $pendingAmount = PayrollSubmission::whereIn('status', ['pending_payment', 'overdue'])
            ->sum('total_with_penalty');

        // Average salary (from all paid submissions)
        $totalWorkers = PayrollWorker::whereHas('submission', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                  ->where('month', $currentMonth);
        })->count();

        $averageSalary = $totalWorkers > 0
            ? PayrollWorker::whereHas('submission', function ($query) use ($currentYear, $currentMonth) {
                $query->where('year', $currentYear)
                      ->where('month', $currentMonth);
            })->avg('net_salary')
            : 0;

        // Total hours worked
        $totalHours = PayrollWorker::whereHas('submission', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                  ->where('month', $currentMonth);
        })->sum(DB::raw('regular_hours + ot_normal_hours + ot_rest_hours + ot_public_hours'));

        // Overtime hours
        $overtimeHours = PayrollWorker::whereHas('submission', function ($query) use ($currentYear, $currentMonth) {
            $query->where('year', $currentYear)
                  ->where('month', $currentMonth);
        })->sum(DB::raw('ot_normal_hours + ot_rest_hours + ot_public_hours'));

        // Completed and pending payments
        $completedPayments = PayrollPayment::where('status', 'completed')
            ->whereYear('completed_at', $currentYear)
            ->whereMonth('completed_at', $currentMonth)
            ->count();

        $pendingPayments = PayrollSubmission::whereIn('status', ['pending_payment', 'overdue'])
            ->count();

        $this->stats = [
            'total_paid' => $totalPaid,
            'pending_amount' => $pendingAmount,
            'average_salary' => round($averageSalary, 2),
            'total_hours' => round($totalHours, 0),
            'completed_payments' => $completedPayments,
            'pending_payments' => $pendingPayments,
            'overtime_hours' => round($overtimeHours, 0),
        ];
    }

    protected function loadClientPayments()
    {
        $this->clientPayments = [
            [
                'client' => 'Miqabina Sdn Bhd',
                'workers' => 12,
                'hours' => 2112,
                'basic_salary' => 38400,
                'overtime' => 4200,
                'allowances' => 2400,
                'deductions' => 200,
                'total' => 45200,
                'status' => 'Paid',
            ],
            [
                'client' => 'WCT Berhad',
                'workers' => 8,
                'hours' => 1408,
                'basic_salary' => 27200,
                'overtime' => 3100,
                'allowances' => 1600,
                'deductions' => 0,
                'total' => 32100,
                'status' => 'Paid',
            ],
            [
                'client' => 'Chuan Luck Piling Sdn Bhd',
                'workers' => 6,
                'hours' => 1056,
                'basic_salary' => 24000,
                'overtime' => 2800,
                'allowances' => 1200,
                'deductions' => 500,
                'total' => 28500,
                'status' => 'Pending',
            ],
            [
                'client' => 'Best Stone Sdn Bhd',
                'workers' => 15,
                'hours' => 2640,
                'basic_salary' => 44000,
                'overtime' => 5600,
                'allowances' => 3000,
                'deductions' => 0,
                'total' => 52800,
                'status' => 'Paid',
            ],
            [
                'client' => 'AIMA Construction Sdn Bhd',
                'workers' => 5,
                'hours' => 880,
                'basic_salary' => 16000,
                'overtime' => 1800,
                'allowances' => 1000,
                'deductions' => 100,
                'total' => 18900,
                'status' => 'Pending',
            ],
        ];
    }

    protected function loadTopWorkers()
    {
        $this->topWorkers = [
            ['rank' => 1, 'employee_id' => 'EMP004', 'name' => 'Mojahidul Rohim', 'position' => 'General Worker', 'client' => 'Best Stone', 'hours' => 192, 'earned' => 5100],
            ['rank' => 2, 'employee_id' => 'EMP001', 'name' => 'Jefri Aldi Kurniawan', 'position' => 'General Worker', 'client' => 'Miqabina', 'hours' => 184, 'earned' => 3900],
            ['rank' => 3, 'employee_id' => 'EMP003', 'name' => 'Chit Win Maung', 'position' => 'General Worker', 'client' => 'Chuan Luck Piling', 'hours' => 176, 'earned' => 3200],
            ['rank' => 4, 'employee_id' => 'EMP006', 'name' => 'Heri Siswanto', 'position' => 'Carpenter', 'client' => 'Miqabina', 'hours' => 180, 'earned' => 2850],
            ['rank' => 5, 'employee_id' => 'EMP005', 'name' => 'Ghulam Abbas', 'position' => 'General Worker', 'client' => 'Miqabina', 'hours' => 176, 'earned' => 2400],
        ];
    }

    protected function loadChartData()
    {
        $this->chartData = [
            'trend' => [
                'labels' => ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
                'data' => [502000, 475000, 490000, 468000, 485000, 486250],
            ],
            'distribution' => [
                'labels' => ['Miqabina', 'WCT', 'Chuan Luck Piling', 'Best Stone', 'AIMA Construction'],
                'data' => [45200, 32100, 28500, 52800, 18900],
            ],
        ];
    }

    public function applyFilters()
    {
        // TODO: Implement filter logic
        session()->flash('success', 'Filters applied successfully!');
    }

    public function render()
    {
        return view('livewire.admin.report');
    }
}
