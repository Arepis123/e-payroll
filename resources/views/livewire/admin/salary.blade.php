<div class="flex h-full w-full flex-1 flex-col gap-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Payroll Management</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">View and manage payroll submissions and payment status</p>
        </div>
    </div>

    <!-- Statistics Summary -->
    <div class="grid gap-4 md:grid-cols-4">
        <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Submissions</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_submissions'] ?? 0 }}</p>
                </div>
                <flux:icon.document-text class="size-8 text-blue-600 dark:text-blue-400" />
            </div>
        </flux:card>

        <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Grand Total (incl. Service & SST)</p>
                    <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM {{ number_format($stats['grand_total'] ?? 0, 2) }}</p>
                </div>
                <flux:icon.wallet class="size-8 text-purple-600 dark:text-purple-400" />
            </div>
        </flux:card>

        <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Completed</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['completed'] ?? 0 }}</p>
                </div>
                <flux:icon.check-circle class="size-8 text-green-600 dark:text-green-400" />
            </div>
        </flux:card>

        <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending</p>
                    <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <flux:icon.clock class="size-8 text-orange-600 dark:text-orange-400" />
            </div>
        </flux:card>
    </div>

    <!-- Submissions Table -->
    <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">All Submissions</h2>
            <div class="flex">
                <flux:button variant="ghost" size="sm" wire:click="export">
                    <flux:icon.arrow-down-tray class="size-4 inline" />
                    Export
                </flux:button>
                <flux:button variant="ghost" size="sm" wire:click="toggleFilters">
                    <flux:icon.funnel class="size-4 inline" />
                    Filter
                </flux:button>
            </div>
        </div>

        <!-- Filters and Search -->
        @if($showFilters)
        <div class="mb-6" x-data x-transition>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5">
                <div>
                    <flux:input
                        wire:model.live="search"
                        placeholder="Search by ID or contractor..."
                        icon="magnifying-glass"
                        size="sm"
                    />
                </div>
                <div>
                    <flux:select wire:model.live="contractorFilter" variant="listbox" placeholder="Filter by Contractor" size="sm">
                        <flux:select.option value="">All Contractors</flux:select.option>
                        @foreach($contractors as $clabNo => $name)
                            <flux:select.option value="{{ $clabNo }}">{{ $name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model.live="statusFilter" variant="listbox" placeholder="Filter by Status" size="sm">
                        <flux:select.option value="">All Statuses</flux:select.option>
                        <flux:select.option value="completed">Completed</flux:select.option>
                        <flux:select.option value="pending">Pending</flux:select.option>
                        <flux:select.option value="draft">Draft</flux:select.option>
                    </flux:select>
                </div>
                <div>
                    <flux:select wire:model.live="paymentStatusFilter" variant="listbox" placeholder="Filter by Payment" size="sm">
                        <flux:select.option value="">All Payment Statuses</flux:select.option>
                        <flux:select.option value="paid">Paid</flux:select.option>
                        <flux:select.option value="awaiting">Awaiting Payment</flux:select.option>
                    </flux:select>
                </div>
                <div>
                    <flux:button variant="filled" size="sm" wire:click="clearFilters">
                        <flux:icon.x-mark class="size-4 inline" />
                        Clear
                    </flux:button>
                </div>
            </div>
        </div>
        @endif

        <flux:table>
            <flux:table.columns>
                <flux:table.column align="center"><span class="text-center text-xs font-medium text-zinc-600 dark:text-zinc-400">No</span></flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'id'" :direction="$sortDirection" wire:click="sortByColumn('id')"><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Submission ID</span></flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'contractor_clab_no'" :direction="$sortDirection" wire:click="sortByColumn('contractor_clab_no')"><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Contractor</span></flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'total_workers'" :direction="$sortDirection" wire:click="sortByColumn('total_workers')"><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Workers</span></flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'month'" :direction="$sortDirection" wire:click="sortByColumn('month')"><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Period</span></flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'grand_total'" :direction="$sortDirection" wire:click="sortByColumn('grand_total')"><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Grand Total</span></flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'status'" :direction="$sortDirection" wire:click="sortByColumn('status')"><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</span></flux:table.column>
                <flux:table.column><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Payment</span></flux:table.column>
                <flux:table.column sortable :sorted="$sortBy === 'submitted_at'" :direction="$sortDirection" wire:click="sortByColumn('submitted_at')"><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Submitted</span></flux:table.column>
                <flux:table.column><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</span></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($submissions as $index => $submission)
                    <flux:table.rows :key="$submission->id">
                        <flux:table.cell>{{ $pagination['from'] + $index }}</flux:table.cell>

                        <flux:table.cell variant="strong">
                            #PAY{{ str_pad($submission->id, 6, '0', STR_PAD_LEFT) }}
                        </flux:table.cell>

                        <flux:table.cell variant="strong" class="max-w-xs truncate">
                            {{ $submission->user ? $submission->user->name : 'Client ' . $submission->contractor_clab_no }}
                        </flux:table.cell>

                        <flux:table.cell variant="strong">
                            {{ $submission->total_workers }} {{ Str::plural('worker', $submission->total_workers) }}
                        </flux:table.cell>

                        <flux:table.cell variant="strong">
                            {{ $submission->month_year }}
                        </flux:table.cell>

                        <flux:table.cell variant="strong">
                            <div class="text-xs text-zinc-600 dark:text-zinc-400">
                                Total: RM {{ number_format($submission->total_amount, 2) }}<br>
                                + Service: RM {{ number_format($submission->service_charge, 2) }}<br>
                                + SST: RM {{ number_format($submission->sst, 2) }}
                            </div>
                            <div class="font-semibold text-zinc-900 dark:text-zinc-100 mt-1">
                                RM {{ number_format($submission->grand_total, 2) }}
                            </div>
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($submission->status === 'paid')
                                <flux:badge color="green" size="sm" inset="top bottom">Completed</flux:badge>
                            @elseif($submission->status === 'pending_payment' || $submission->status === 'overdue')
                                <flux:badge color="yellow" size="sm" inset="top bottom">Pending</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm" inset="top bottom">Draft</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell>
                            @if($submission->payment && $submission->payment->status === 'completed')
                                <flux:badge color="green" size="sm" icon="check" inset="top bottom">Paid</flux:badge>
                            @else
                                <flux:badge color="orange" size="sm" icon="clock" inset="top bottom">Awaiting</flux:badge>
                            @endif
                        </flux:table.cell>

                        <flux:table.cell variant="strong">
                            {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y') : '-' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" inset="top bottom" />
                                <flux:menu>
                                    <flux:menu.item icon="eye" href="#">View Details</flux:menu.item>
                                    @if($submission->payment && $submission->payment->status === 'completed')
                                        <flux:menu.item icon="document">Download Receipt</flux:menu.item>
                                        <flux:menu.item icon="printer">Print Payslip</flux:menu.item>
                                    @else
                                        <flux:menu.item icon="credit-card">Process Payment</flux:menu.item>
                                    @endif
                                    @if($submission->status === 'draft')
                                        <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Delete</flux:menu.item>
                                    @endif
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.rows>
                @empty
                    <flux:table.rows>
                        <flux:table.cell variant="strong" colspan="10" class="text-center">
                            @if($search || $contractorFilter || $statusFilter || $paymentStatusFilter)
                                No submissions found matching your filters.
                            @else
                                No payroll submissions have been created yet.
                            @endif
                        </flux:table.cell>
                    </flux:table.rows>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <!-- Pagination -->
        @if($pagination['total'] > 0)
            <div class="mt-4 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-700 pt-4">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total'] }} results
                </p>
                <div class="flex items-center gap-2">
                    @if($pagination['current_page'] > 1)
                        <flux:button variant="ghost" size="sm" wire:click="$set('page', {{ $pagination['current_page'] - 1 }})">Previous</flux:button>
                    @else
                        <flux:button variant="ghost" size="sm" disabled>Previous</flux:button>
                    @endif

                    @for($i = 1; $i <= $pagination['last_page']; $i++)
                        @if($i == $pagination['current_page'])
                            <flux:button variant="primary" size="xs">{{ $i }}</flux:button>
                        @else
                            <flux:button variant="ghost" size="xs" wire:click="$set('page', {{ $i }})">{{ $i }}</flux:button>
                        @endif
                    @endfor

                    @if($pagination['current_page'] < $pagination['last_page'])
                        <flux:button variant="ghost" size="sm" wire:click="$set('page', {{ $pagination['current_page'] + 1 }})">Next</flux:button>
                    @else
                        <flux:button variant="ghost" size="sm" disabled>Next</flux:button>
                    @endif
                </div>
            </div>
        @endif
    </flux:card>
</div>
