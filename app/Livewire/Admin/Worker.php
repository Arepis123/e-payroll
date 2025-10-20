<?php

namespace App\Livewire\Admin;

use App\Exports\WorkersExport;
use App\Models\PayrollWorker;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class Worker extends Component
{
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $clientFilter = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $positionFilter = '';

    #[Url]
    public $page = 1;

    #[Url]
    public $sortBy = 'name';

    #[Url]
    public $sortDirection = 'asc';

    public $stats = [];
    public $workers = [];
    public $showFilters = true;
    public $clients = [];
    public $positions = [];
    public $perPage = 10;

    public function mount()
    {
        $this->loadStats();
        $this->loadClients();
        $this->loadPositions();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->clientFilter = '';
        $this->positionFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function resetPage()
    {
        $this->page = 1;
    }

    public function export()
    {
        $allWorkers = $this->getWorkersData();

        $fileName = 'workers_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new WorkersExport($allWorkers), $fileName);
    }

    protected function loadClients()
    {
        // Get unique clients (users with role 'client' who have submitted payrolls)
        $this->clients = PayrollWorker::with('payrollSubmission.user')
            ->get()
            ->pluck('payrollSubmission.user')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    protected function loadPositions()
    {
        // Get unique positions from actual worker data
        $allWorkers = $this->getAllWorkersForFilters();

        $this->positions = $allWorkers
            ->pluck('position')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    /**
     * Get all workers data without filters for populating filter options
     */
    protected function getAllWorkersForFilters()
    {
        // Get unique workers with their latest submission data (no filters applied)
        $query = PayrollWorker::select(
            'worker_id',
            'worker_name',
            'worker_passport',
            'basic_salary',
            'payroll_submission_id',
            DB::raw('MAX(created_at) as latest_date')
        )
        ->groupBy('worker_id', 'worker_name', 'worker_passport', 'basic_salary', 'payroll_submission_id');

        $workers = $query->with(['payrollSubmission.user', 'worker.country'])
            ->orderBy('latest_date', 'desc')
            ->get();

        // Transform the data
        return $workers->map(function ($worker) {
            return [
                'id' => $worker->worker_id,
                'employee_id' => $worker->worker_id,
                'name' => $worker->worker_name,
                'passport' => $worker->worker_passport,
                'position' => 'General Worker', // TODO: Add position field to database
                'client' => $worker->payrollSubmission && $worker->payrollSubmission->user
                    ? $worker->payrollSubmission->user->name
                    : 'N/A',
                'salary' => $worker->basic_salary,
                'passport_expiry' => $worker->worker && $worker->worker->wkr_passexp
                    ? $worker->worker->wkr_passexp->format('d/m/Y')
                    : 'N/A',
                'permit_expiry' => $worker->worker && $worker->worker->wkr_permitexp
                    ? $worker->worker->wkr_permitexp->format('d/m/Y')
                    : 'N/A',
                'country' => $worker->worker && $worker->worker->country
                    ? $worker->worker->country->cty_desc
                    : 'N/A',
            ];
        })->unique('employee_id');
    }

    protected function loadStats()
    {
        // Get unique workers from payroll_workers table
        $totalWorkers = PayrollWorker::distinct('worker_id')->count('worker_id');

        // For now, we'll calculate based on recent activity
        // Active workers are those who appeared in submissions within last 3 months
        $threeMonthsAgo = now()->subMonths(3);
        $activeWorkers = PayrollWorker::whereHas('submission', function ($query) use ($threeMonthsAgo) {
            $query->where('created_at', '>=', $threeMonthsAgo);
        })->distinct('worker_id')->count('worker_id');

        // Mock data for on_leave and inactive until we have worker status tracking
        $this->stats = [
            'total' => $totalWorkers,
            'active' => $activeWorkers,
            'on_leave' => 0, // TODO: Add worker status tracking
            'inactive' => $totalWorkers - $activeWorkers,
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingClientFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPositionFilter()
    {
        $this->resetPage();
    }

    public function getWorkersData()
    {
        // Get unique workers with their latest submission data
        $query = PayrollWorker::select(
            'worker_id',
            'worker_name',
            'worker_passport',
            'basic_salary',
            'payroll_submission_id',
            DB::raw('MAX(created_at) as latest_date')
        )
        ->groupBy('worker_id', 'worker_name', 'worker_passport', 'basic_salary', 'payroll_submission_id');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('worker_name', 'like', '%' . $this->search . '%')
                  ->orWhere('worker_id', 'like', '%' . $this->search . '%')
                  ->orWhere('worker_passport', 'like', '%' . $this->search . '%');
            });
        }

        // Apply client filter
        if ($this->clientFilter) {
            $query->whereHas('payrollSubmission.user', function ($q) {
                $q->where('id', $this->clientFilter);
            });
        }

        // Get the workers with their submission data
        $workers = $query->with(['payrollSubmission.user', 'worker.country'])
            ->orderBy('latest_date', 'desc')
            ->get();

        // Transform the data
        $transformedWorkers = $workers->map(function ($worker) {
            // Determine status based on last activity
            $lastActivity = $worker->latest_date ? \Carbon\Carbon::parse($worker->latest_date) : null;
            $isActive = $lastActivity && $lastActivity->isAfter(now()->subMonths(3));

            $status = $isActive ? 'Active' : 'Inactive';

            // Get client name from submission
            $clientName = $worker->payrollSubmission && $worker->payrollSubmission->user
                ? $worker->payrollSubmission->user->name
                : 'N/A';

            return [
                'id' => $worker->worker_id,
                'employee_id' => $worker->worker_id,
                'name' => $worker->worker_name,
                'passport' => $worker->worker_passport,
                'position' => 'General Worker', // TODO: Add position field to database
                'client' => $clientName,
                'salary' => $worker->basic_salary,
                'status' => $status,
                'passport_expiry' => $worker->worker && $worker->worker->wkr_passexp
                    ? $worker->worker->wkr_passexp->format('d/m/Y')
                    : 'N/A',
                'permit_expiry' => $worker->worker && $worker->worker->wkr_permitexp
                    ? $worker->worker->wkr_permitexp->format('d/m/Y')
                    : 'N/A',
                'country' => $worker->worker && $worker->worker->country
                    ? $worker->worker->country->cty_desc
                    : 'N/A',
            ];
        })->unique('employee_id');

        // Apply status filter after transformation
        if ($this->statusFilter) {
            $transformedWorkers = $transformedWorkers->filter(function ($worker) {
                return $worker['status'] === $this->statusFilter;
            });
        }

        // Apply position filter after transformation
        if ($this->positionFilter) {
            $transformedWorkers = $transformedWorkers->filter(function ($worker) {
                return $worker['position'] === $this->positionFilter;
            });
        }

        // Apply sorting
        $transformedWorkers = $transformedWorkers->sort(function ($a, $b) {
            $primaryA = match($this->sortBy) {
                'name' => strtolower($a['name']),
                'passport' => strtolower($a['passport']),
                'position' => strtolower($a['position']),
                'country' => strtolower($a['country']),
                'client' => strtolower($a['client']),
                'passport_expiry' => strtotime($a['passport_expiry']),
                'permit_expiry' => strtotime($a['permit_expiry']),
                'status' => $a['status'] === 'Active' ? 0 : 1,
                default => strtolower($a['name']),
            };

            $primaryB = match($this->sortBy) {
                'name' => strtolower($b['name']),
                'passport' => strtolower($b['passport']),
                'position' => strtolower($b['position']),
                'country' => strtolower($b['country']),
                'client' => strtolower($b['client']),
                'passport_expiry' => strtotime($b['passport_expiry']),
                'permit_expiry' => strtotime($b['permit_expiry']),
                'status' => $b['status'] === 'Active' ? 0 : 1,
                default => strtolower($b['name']),
            };

            $comparison = $primaryA <=> $primaryB;

            // If primary values are equal, sort by name as secondary
            if ($comparison === 0 && $this->sortBy !== 'name') {
                $comparison = strtolower($a['name']) <=> strtolower($b['name']);
            }

            return $this->sortDirection === 'desc' ? -$comparison : $comparison;
        })->values();

        return $transformedWorkers;
    }

    public function render()
    {
        $allWorkers = $this->getWorkersData();

        // Pagination
        $total = $allWorkers->count();
        $this->workers = $allWorkers->slice(($this->page - 1) * $this->perPage, $this->perPage)->values();

        $pagination = [
            'current_page' => $this->page,
            'per_page' => $this->perPage,
            'total' => $total,
            'last_page' => ceil($total / $this->perPage),
            'from' => (($this->page - 1) * $this->perPage) + 1,
            'to' => min($this->page * $this->perPage, $total),
        ];

        return view('livewire.admin.worker', [
            'workers' => $this->workers,
            'pagination' => $pagination,
        ]);
    }
}
