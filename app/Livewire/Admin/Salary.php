<?php

namespace App\Livewire\Admin;

use App\Models\PayrollSubmission;
use App\Models\PayrollPayment;
use App\Exports\PayrollSubmissionsExport;
use Flux\Flux;
use Maatwebsite\Excel\Facades\Excel;
use Livewire\Component;
use Livewire\Attributes\Url;

class Salary extends Component
{

    public $stats = [];

    #[Url]
    public $contractorFilter = '';

    #[Url]
    public $statusFilter = '';

    #[Url]
    public $paymentStatusFilter = '';

    #[Url]
    public $search = '';

    #[Url]
    public $page = 1;

    public $contractors = [];
    public $perPage = 10;
    public $showFilters = true;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Payment Log Modal
    public $showPaymentLog = false;
    public $selectedSubmission = null;

    public function mount()
    {
        $this->loadStats();
        $this->loadContractors();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function export()
    {
        // Get all submissions based on current filters
        $submissions = $this->getSubmissions();

        // Check if there are submissions to export
        if ($submissions->isEmpty()) {
            Flux::toast(
                variant: 'warning',
                heading: 'No data to export',
                text: 'No payroll submissions found matching your filters.'
            );
            return;
        }

        // Prepare filter information for export
        $filters = [
            'search' => $this->search,
            'contractor' => $this->contractorFilter ? ($this->contractors[$this->contractorFilter] ?? $this->contractorFilter) : null,
            'status' => $this->statusFilter,
            'payment_status' => $this->paymentStatusFilter,
        ];

        // Generate filename with current date
        $filename = 'payroll_submissions_' . now()->format('Y-m-d_His') . '.xlsx';

        // Return Excel download
        return Excel::download(new PayrollSubmissionsExport($submissions, $filters), $filename);
    }

    public function openPaymentLog($submissionId)
    {
        $this->selectedSubmission = PayrollSubmission::with(['user', 'payment', 'payments'])
            ->findOrFail($submissionId);
        $this->showPaymentLog = true;
    }

    public function closePaymentLog()
    {
        $this->showPaymentLog = false;
        $this->selectedSubmission = null;
    }

    public function resetPage()
    {
        $this->page = 1;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingContractorFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatusFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->contractorFilter = '';
        $this->statusFilter = '';
        $this->paymentStatusFilter = '';
        $this->search = '';
        $this->resetPage();
    }

    protected function loadStats()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Total submissions this month
        $totalSubmissions = PayrollSubmission::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->count();

        // Grand total this month (including service charge and SST)
        $grandTotal = PayrollSubmission::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('grand_total');

        // Completed submissions (paid)
        $completed = PayrollSubmission::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->where('status', 'paid')
            ->count();

        // Pending submissions
        $pending = PayrollSubmission::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->whereIn('status', ['pending_payment', 'draft', 'overdue'])
            ->count();

        $this->stats = [
            'total_submissions' => $totalSubmissions,
            'grand_total' => $grandTotal,
            'completed' => $completed,
            'pending' => $pending,
        ];
    }

    protected function loadContractors()
    {
        // Get unique contractors from submissions
        $this->contractors = PayrollSubmission::with('user')
            ->get()
            ->pluck('user')
            ->filter()
            ->unique('id')
            ->sortBy('name')
            ->pluck('name', 'contractor_clab_no')
            ->toArray();
    }

    protected function getSubmissions()
    {
        $query = PayrollSubmission::with(['user', 'payment']);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply contractor filter
        if ($this->contractorFilter) {
            $query->where('contractor_clab_no', $this->contractorFilter);
        }

        // Apply status filter
        if ($this->statusFilter) {
            if ($this->statusFilter === 'completed') {
                $query->where('status', 'paid');
            } elseif ($this->statusFilter === 'pending') {
                $query->whereIn('status', ['pending_payment', 'overdue']);
            } elseif ($this->statusFilter === 'draft') {
                $query->where('status', 'draft');
            }
        }

        // Apply payment status filter
        if ($this->paymentStatusFilter) {
            if ($this->paymentStatusFilter === 'paid') {
                $query->whereHas('payment', function ($paymentQuery) {
                    $paymentQuery->where('status', 'completed');
                });
            } elseif ($this->paymentStatusFilter === 'awaiting') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('payment')
                      ->orWhereHas('payment', function ($paymentQuery) {
                          $paymentQuery->where('status', '!=', 'completed');
                      });
                });
            }
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->get();
    }

    public function render()
    {
        $allSubmissions = $this->getSubmissions();

        // Manual pagination
        $total = $allSubmissions->count();
        $submissions = $allSubmissions->slice(($this->page - 1) * $this->perPage, $this->perPage)->values();

        $pagination = [
            'current_page' => $this->page,
            'per_page' => $this->perPage,
            'total' => $total,
            'last_page' => ceil($total / $this->perPage),
            'from' => (($this->page - 1) * $this->perPage) + 1,
            'to' => min($this->page * $this->perPage, $total),
        ];

        return view('livewire.admin.salary', [
            'submissions' => $submissions,
            'pagination' => $pagination,
        ]);
    }
}
