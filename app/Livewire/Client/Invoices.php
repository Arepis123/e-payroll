<?php

namespace App\Livewire\Client;

use App\Models\PayrollSubmission;
use Livewire\Component;
use Livewire\Attributes\Url;

class Invoices extends Component
{
    #[Url]
    public $search = '';

    #[Url]
    public $statusFilter = 'all';

    #[Url]
    public $year;

    #[Url]
    public $page = 1;

    #[Url]
    public $sortBy = 'issue_date';

    #[Url]
    public $sortDirection = 'desc';

    public function mount()
    {
        if (!$this->year) {
            $this->year = now()->year;
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedYear()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->resetPage();
    }

    public function resetPage()
    {
        $this->page = 1;
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

    public function render()
    {
        $clabNo = auth()->user()->contractor_clab_no;

        if (!$clabNo) {
            return view('livewire.client.invoices', [
                'error' => 'No contractor CLAB number assigned to your account.',
                'invoices' => collect([]),
                'stats' => [
                    'pending_invoices' => 0,
                    'paid_invoices' => 0,
                    'total_invoiced' => 0,
                ],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0,
                ],
                'availableYears' => collect([]),
            ]);
        }

        // Get all invoices for this contractor
        $allInvoices = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->where('year', $this->year)
            ->with(['payment'])
            ->get();

        // Apply search filter
        if ($this->search) {
            $allInvoices = $allInvoices->filter(function($invoice) {
                $searchLower = strtolower($this->search);
                $invoiceNumber = 'INV-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT);
                return str_contains(strtolower($invoiceNumber), $searchLower) ||
                       str_contains(strtolower($invoice->month_year ?? ''), $searchLower);
            });
        }

        // Apply status filter
        if ($this->statusFilter && $this->statusFilter !== 'all') {
            $allInvoices = $allInvoices->filter(function($invoice) {
                return $invoice->status === $this->statusFilter;
            });
        }

        // Apply sorting
        $allInvoices = $allInvoices->sort(function($a, $b) {
            $primaryA = match($this->sortBy) {
                'invoice_number' => str_pad($a->id, 4, '0', STR_PAD_LEFT),
                'period' => $a->month_year ?? '',
                'workers' => $a->total_workers ?? 0,
                'amount' => $a->total_with_penalty ?? 0,
                'issue_date' => $a->submitted_at ? $a->submitted_at->timestamp : 0,
                'due_date' => $a->payment_deadline ? $a->payment_deadline->timestamp : 0,
                'status' => match($a->status) {
                    'overdue' => 0,
                    'pending_payment' => 1,
                    'paid' => 2,
                    'draft' => 3,
                    default => 4,
                },
                default => $a->submitted_at ? $a->submitted_at->timestamp : 0,
            };

            $primaryB = match($this->sortBy) {
                'invoice_number' => str_pad($b->id, 4, '0', STR_PAD_LEFT),
                'period' => $b->month_year ?? '',
                'workers' => $b->total_workers ?? 0,
                'amount' => $b->total_with_penalty ?? 0,
                'issue_date' => $b->submitted_at ? $b->submitted_at->timestamp : 0,
                'due_date' => $b->payment_deadline ? $b->payment_deadline->timestamp : 0,
                'status' => match($b->status) {
                    'overdue' => 0,
                    'pending_payment' => 1,
                    'paid' => 2,
                    'draft' => 3,
                    default => 4,
                },
                default => $b->submitted_at ? $b->submitted_at->timestamp : 0,
            };

            // Primary sort comparison
            $comparison = $primaryA <=> $primaryB;

            // If primary values are equal, sort by issue date as secondary (most recent first)
            if ($comparison === 0) {
                $dateA = $a->submitted_at ? $a->submitted_at->timestamp : 0;
                $dateB = $b->submitted_at ? $b->submitted_at->timestamp : 0;
                $comparison = $dateB <=> $dateA;
            }

            // Apply sort direction
            return $this->sortDirection === 'desc' ? -$comparison : $comparison;
        })->values();

        // Calculate statistics
        $allSubmissions = PayrollSubmission::where('contractor_clab_no', $clabNo)->get();

        $pendingInvoices = $allSubmissions->whereIn('status', ['pending_payment', 'overdue'])->count();
        $paidInvoices = $allSubmissions->where('status', 'paid')->count();
        $totalInvoiced = $allSubmissions->sum('total_with_penalty');

        // Available years for filter
        $availableYears = PayrollSubmission::where('contractor_clab_no', $clabNo)
            ->selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $stats = [
            'pending_invoices' => $pendingInvoices,
            'paid_invoices' => $paidInvoices,
            'total_invoiced' => $totalInvoiced,
        ];

        // Pagination
        $perPage = 10;
        $total = $allInvoices->count();
        $invoices = $allInvoices->slice(($this->page - 1) * $perPage, $perPage)->values();

        $pagination = [
            'current_page' => $this->page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
            'from' => (($this->page - 1) * $perPage) + 1,
            'to' => min($this->page * $perPage, $total),
        ];

        return view('livewire.client.invoices', [
            'invoices' => $invoices,
            'stats' => $stats,
            'pagination' => $pagination,
            'availableYears' => $availableYears,
        ])->layout('components.layouts.app', ['title' => __('Invoices')]);
    }
}
