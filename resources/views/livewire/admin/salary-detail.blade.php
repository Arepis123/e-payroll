<div class="flex h-full w-full flex-1 flex-col gap-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" icon="arrow-left" href="{{ route('payroll') }}" />
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                    Payroll Details #PAY{{ str_pad($submission->id, 6, '0', STR_PAD_LEFT) }}
                </h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Submission for {{ $submission->month_year }}
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <flux:button variant="filled" size="sm" wire:click="exportWorkerList" icon="arrow-down-tray" icon-variant="outline">
                Export
            </flux:button>
            @if($submission->payment && $submission->payment->status === 'completed')
                <flux:button variant="filled" size="sm" wire:click="downloadReceipt" icon="document" icon-variant="outline">
                    Receipt
                </flux:button>
                <flux:button variant="filled" size="sm" wire:click="printPayslip" icon="printer" icon-variant="outline">
                    Payslip
                </flux:button>
                <flux:button variant="filled" size="sm" wire:click="viewPaymentProof" icon="eye" icon-variant="outline">
                    Payment Proof
                </flux:button>
            @else
                <flux:button variant="filled" size="sm" wire:click="sendReminder" icon="bell" icon-variant="outline">
                    Send Reminder
                </flux:button>
                <flux:button variant="filled" size="sm" wire:click="markAsPaid" icon="check-circle" icon-variant="outline">
                    Mark as Paid
                </flux:button>
            @endif
        </div>
    </div>

    <!-- Status Badges -->
    <div class="flex items-center gap-3">
        @if($submission->status === 'paid')
            <flux:badge color="green" size="sm" icon="check-circle" inset="top bottom">Completed</flux:badge>
        @elseif($submission->status === 'pending_payment' || $submission->status === 'overdue')
            <flux:badge color="yellow" size="sm" icon="clock" inset="top bottom">Pending Payment</flux:badge>
        @else
            <flux:badge color="zinc" size="sm" inset="top bottom">Draft</flux:badge>
        @endif

        @if($submission->payment && $submission->payment->status === 'completed')
            <flux:badge color="green" size="sm" icon="check" inset="top bottom">Payment Received</flux:badge>
        @else
            <flux:badge color="orange" size="sm" icon="clock" inset="top bottom">Awaiting Payment</flux:badge>
        @endif

        @if($submission->isOverdue())
            <flux:badge color="red" size="sm" icon="exclamation-triangle" inset="top bottom">Overdue</flux:badge>
        @endif
    </div>

    <!-- Summary Cards -->
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="space-y-2">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Workers</p>
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_workers'] }}</p>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="space-y-2">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Current Month OT Hours</p>
                <p class="text-2xl font-bold ">{{ number_format($stats['total_ot_hours'], 2) }}</p>
                <p class="text-xs ">To be paid next month</p>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="space-y-2">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Grand Total</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    RM {{ number_format($submission->grand_total, 2) }}
                </p>
            </div>
        </flux:card>

        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="space-y-2">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Payment Deadline</p>
                <p class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                    {{ $submission->payment_deadline->format('d M Y') }}
                </p>
                @if(!$submission->isOverdue() && $submission->status !== 'paid')
                    <p class="text-xs text-zinc-500 dark:text-zinc-500">
                        {{ abs($submission->daysUntilDeadline()) }} days remaining
                    </p>
                @elseif($submission->isOverdue())
                    <p class="text-xs text-red-600 dark:text-red-400">
                        {{ abs($submission->daysUntilDeadline()) }} days overdue
                    </p>
                @endif
            </div>
        </flux:card>
    </div>

    <!-- OT Payment Flow Information -->
    <div class="grid gap-4 md:grid-cols-2">
        <!-- Previous Month OT (Paid This Month) -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="flex items-start gap-3">
                <flux:icon.check-circle class="size-6 text-green-600 dark:text-green-400 mt-0.5" />
                <div class="flex-1">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Previous Month OT Paid</h3>
                    @if($previousSubmission)
                        <div class="space-y-2 text-sm">
                            <p class="text-zinc-600 dark:text-zinc-400">
                                OT from <span class="font-medium text-green-600 dark:text-green-400">{{ $previousSubmission->month_year }}</span> included in this month's payment
                            </p>
                            <div class="grid grid-cols-2 gap-2 mt-3">
                                <div class="bg-green-50 dark:bg-green-900/20 p-2 rounded">
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">Total Hours</p>
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($previousOtStats['total_ot_hours'], 2) }}h</p>
                                </div>
                                <div class="bg-green-50 dark:bg-green-900/20 p-2 rounded">
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">Total Amount</p>
                                    <p class="text-lg font-bold text-green-600 dark:text-green-400">RM {{ number_format($previousOtStats['total_ot_pay'], 2) }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">No previous month OT data found</p>
                    @endif
                </div>
            </div>
        </flux:card>

        <!-- Current Month OT (To be Paid Next Month) -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="flex items-start gap-3">
                <flux:icon.clock class="size-6 text-orange-600 dark:text-orange-400 mt-0.5" />
                <div class="flex-1">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-2">Current Month OT Deferred</h3>
                    <div class="space-y-2 text-sm">
                        <p class="text-zinc-600 dark:text-zinc-400">
                            OT from <span class="font-medium text-orange-600 dark:text-orange-400">{{ $submission->month_year }}</span> will be paid next month
                        </p>
                        <div class="grid grid-cols-2 gap-2 mt-3">
                            <div class="bg-orange-50 dark:bg-orange-900/20 p-2 rounded">
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Total Hours</p>
                                <p class="text-lg font-bold text-orange-600 dark:text-orange-400">{{ number_format($stats['total_ot_hours'], 2) }}h</p>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-900/20 p-2 rounded">
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Total Amount</p>
                                <p class="text-lg font-bold text-orange-600 dark:text-orange-400">RM {{ number_format($stats['total_ot_pay'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Submission & Payment Information -->
    <div class="grid gap-4 md:grid-cols-2">
        <!-- Submission Information -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Submission Information</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Submission ID:</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        #PAY{{ str_pad($submission->id, 6, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Contractor:</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        {{ $submission->user ? $submission->user->name : 'Client ' . $submission->contractor_clab_no }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">CLAB No:</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        {{ $submission->contractor_clab_no }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Period:</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        {{ $submission->month_year }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Submitted At:</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y, H:i') : '-' }}
                    </span>
                </div>
                @if($submission->paid_at)
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Paid At:</span>
                    <span class="text-sm font-medium text-green-600 dark:text-green-400">
                        {{ $submission->paid_at->format('d M Y, H:i') }}
                    </span>
                </div>
                @endif
            </div>
        </flux:card>

        <!-- Amount Breakdown -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Amount Breakdown</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Total Amount (Workers):</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        RM {{ number_format($submission->total_amount, 2) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Service Charge:</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        RM {{ number_format($submission->service_charge, 2) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">SST (8%):</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        RM {{ number_format($submission->sst, 2) }}
                    </span>
                </div>
                <div class="border-t border-zinc-200 dark:border-zinc-700 pt-3 flex justify-between">
                    <span class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Grand Total:</span>
                    <span class="text-base font-bold text-green-600 dark:text-green-400">
                        RM {{ number_format($submission->grand_total, 2) }}
                    </span>
                </div>
                @if($submission->has_penalty)
                <div class="flex justify-between">
                    <span class="text-sm text-red-600 dark:text-red-400">Penalty (8%):</span>
                    <span class="text-sm font-medium text-red-600 dark:text-red-400">
                        + RM {{ number_format($submission->penalty_amount, 2) }}
                    </span>
                </div>
                <div class="border-t border-red-200 dark:border-red-800 pt-3 flex justify-between">
                    <span class="text-base font-semibold text-red-900 dark:text-red-100">Total with Penalty:</span>
                    <span class="text-base font-bold text-red-600 dark:text-red-400">
                        RM {{ number_format($submission->total_with_penalty, 2) }}
                    </span>
                </div>
                @endif
            </div>
        </flux:card>
    </div>

    <!-- Payment Information -->
    @if($submission->payment)
    <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
        <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Payment Information</h2>
        <div class="grid gap-4 md:grid-cols-3">
            <div>
                <span class="text-sm text-zinc-600 dark:text-zinc-400">Payment Method:</span>
                <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                    {{ strtoupper($submission->payment->payment_method ?? 'N/A') }}
                </p>
            </div>
            <div>
                <span class="text-sm text-zinc-600 dark:text-zinc-400">Transaction ID:</span>
                <p class="mt-1 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                    {{ $submission->payment->transaction_id ?? 'N/A' }}
                </p>
            </div>
            <div>
                <span class="text-sm text-zinc-600 dark:text-zinc-400">Status:</span>
                <p class="mt-1">
                    @if($submission->payment->status === 'completed')
                        <flux:badge color="green" size="sm" icon="check">Completed</flux:badge>
                    @elseif($submission->payment->status === 'pending')
                        <flux:badge color="yellow" size="sm" icon="clock">Pending</flux:badge>
                    @else
                        <flux:badge color="red" size="sm" icon="x-mark">Failed</flux:badge>
                    @endif
                </p>
            </div>
        </div>
    </flux:card>
    @endif

    <!-- Workers List -->
    <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Workers Payroll</h2>
            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $stats['total_workers'] }} {{ Str::plural('worker', $stats['total_workers']) }}</span>
        </div>

        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">No</span>
                    </flux:table.column>
                    <flux:table.column>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Worker Name</span>
                    </flux:table.column>
                    <flux:table.column>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Passport</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Basic Salary</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Weekday OT<br>(Deferred)</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Rest Day OT<br>(Deferred)</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Public Holiday OT<br>(Deferred)</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Total OT<br>(Next Month)</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Gross Salary</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Deductions</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Net Salary</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Payment</span>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($workers as $index => $worker)
                        <flux:table.rows :key="$worker->id">
                            <flux:table.cell align="center">{{ $index + 1 }}</flux:table.cell>

                            <flux:table.cell variant="strong">
                                {{ $worker->worker_name }}
                            </flux:table.cell>

                            <flux:table.cell>
                                {{ $worker->worker_passport }}
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                RM {{ number_format($worker->basic_salary, 2) }}
                            </flux:table.cell>

                            <!-- Weekday OT -->
                            <flux:table.cell align="center">
                                <div class="text-xs ">
                                    @if($worker->ot_normal_hours > 0)
                                        <div class="font-medium">{{ number_format($worker->ot_normal_hours, 2) }}h</div>
                                        <div class="text-xs">RM {{ number_format($worker->ot_normal_pay, 2) }}</div>
                                    @else
                                        <div class="text-zinc-600 dark:text-zinc-400">-</div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <!-- Rest Day OT -->
                            <flux:table.cell align="center">
                                <div class="text-xs ">
                                    @if($worker->ot_rest_hours > 0)
                                        <div class="font-medium">{{ number_format($worker->ot_rest_hours, 2) }}h</div>
                                        <div class="text-xs">RM {{ number_format($worker->ot_rest_pay, 2) }}</div>
                                    @else
                                        <div class="text-zinc-600 dark:text-zinc-400">-</div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <!-- Public Holiday OT -->
                            <flux:table.cell align="center">
                                <div class="text-xs ">
                                    @if($worker->ot_public_hours > 0)
                                        <div class="font-medium">{{ number_format($worker->ot_public_hours, 2) }}h</div>
                                        <div class="text-xs">RM {{ number_format($worker->ot_public_pay, 2) }}</div>
                                    @else
                                        <div class="text-zinc-600 dark:text-zinc-400">-</div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <!-- Total OT -->
                            <flux:table.cell align="center">
                                <div class="text-xs ">
                                    @if($worker->total_overtime_hours > 0)
                                        <div class="font-semibold">{{ number_format($worker->total_overtime_hours, 2) }}h</div>
                                        <div class="font-medium">RM {{ number_format($worker->total_ot_pay, 2) }}</div>
                                    @else
                                        <div class="text-zinc-600 dark:text-zinc-400">-</div>
                                    @endif
                                </div>
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                RM {{ number_format($worker->gross_salary, 2) }}
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                <div class="text-xs text-red-600 dark:text-red-400">
                                    - RM {{ number_format($worker->total_deductions, 2) }}
                                </div>
                            </flux:table.cell>

                            <flux:table.cell align="right" variant="strong">
                                RM {{ number_format($worker->net_salary, 2) }}
                            </flux:table.cell>

                            <flux:table.cell align="right" variant="strong" class="text-green-600 dark:text-green-400">
                                RM {{ number_format($worker->total_payment, 2) }}
                            </flux:table.cell>
                        </flux:table.rows>
                    @endforeach

                    <!-- Summary Row -->
                    <flux:table.rows class="border-t-2 border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-800">
                        <flux:table.cell colspan="3" variant="strong" class="font-bold">
                            <span class="flex justify-center">TOTAL</span>
                        </flux:table.cell>
                        <flux:table.cell align="right" variant="strong" class="font-bold">
                            RM {{ number_format($stats['total_basic_salary'], 2) }}
                        </flux:table.cell>
                        <!-- Weekday OT Total -->
                        <flux:table.cell align="center" variant="strong" class="font-bold">
                            <div class="text-xs">{{ number_format($workers->sum('ot_normal_hours'), 2) }}h</div>
                            <div class="text-xs">RM {{ number_format($workers->sum('ot_normal_pay'), 2) }}</div>
                        </flux:table.cell>
                        <!-- Rest Day OT Total -->
                        <flux:table.cell align="center" variant="strong" class="font-bold">
                            <div class="text-xs">{{ number_format($workers->sum('ot_rest_hours'), 2) }}h</div>
                            <div class="text-xs">RM {{ number_format($workers->sum('ot_rest_pay'), 2) }}</div>
                        </flux:table.cell>
                        <!-- Public Holiday OT Total -->
                        <flux:table.cell align="center" variant="strong" class="font-bold">
                            <div class="text-xs">{{ number_format($workers->sum('ot_public_hours'), 2) }}h</div>
                            <div class="text-xs">RM {{ number_format($workers->sum('ot_public_pay'), 2) }}</div>
                        </flux:table.cell>
                        <!-- Total OT -->
                        <flux:table.cell align="center" variant="strong" class="font-bold">
                            <div class="text-xs">{{ number_format($stats['total_ot_hours'], 2) }}h</div>
                            <div class="text-xs">RM {{ number_format($stats['total_ot_pay'], 2) }}</div>
                        </flux:table.cell>
                        <flux:table.cell align="right" variant="strong" class="font-bold">
                            RM {{ number_format($stats['total_gross_salary'], 2) }}
                        </flux:table.cell>
                        <flux:table.cell align="right" variant="strong" class="font-bold text-red-600 dark:text-red-400">
                            - RM {{ number_format($stats['total_deductions'], 2) }}
                        </flux:table.cell>
                        <flux:table.cell align="right" variant="strong" class="font-bold">
                            RM {{ number_format($stats['total_net_salary'], 2) }}
                        </flux:table.cell>
                        <flux:table.cell align="right" variant="strong" class="font-bold text-green-600 dark:text-green-400">
                            RM {{ number_format($stats['total_payment'], 2) }}
                        </flux:table.cell>
                    </flux:table.rows>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>

    <!-- Previous Month OT Breakdown (Paid This Month) -->
    @if($previousSubmission && $previousOtStats['total_ot_hours'] > 0)
    <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg border-t-4 border-green-500">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Previous Month OT Breakdown <span class="text-green-600 dark:text-green-400">(Paid this month)</span></h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">This OT was calculated for {{ $previousSubmission->month_year }} and is included in {{ $submission->month_year }}'s payment</p>
        </div>

        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">No</span>
                    </flux:table.column>
                    <flux:table.column>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Worker Name</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Weekday OT</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Weekday Pay</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Rest Day OT</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Rest Day Pay</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Public Holiday OT</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Public Holiday Pay</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Hours</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Total OT Pay</span>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($previousWorkers as $index => $worker)
                        @if($worker->total_overtime_hours > 0)
                        <flux:table.rows :key="'prev-' . $worker->id">
                            <flux:table.cell align="center">{{ $index + 1 }}</flux:table.cell>

                            <flux:table.cell variant="strong">
                                {{ $worker->worker_name }}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                {{ $worker->ot_normal_hours > 0 ? number_format($worker->ot_normal_hours, 2) . 'h' : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                {{ $worker->ot_normal_pay > 0 ? 'RM ' . number_format($worker->ot_normal_pay, 2) : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                {{ $worker->ot_rest_hours > 0 ? number_format($worker->ot_rest_hours, 2) . 'h' : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                {{ $worker->ot_rest_pay > 0 ? 'RM ' . number_format($worker->ot_rest_pay, 2) : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                {{ $worker->ot_public_hours > 0 ? number_format($worker->ot_public_hours, 2) . 'h' : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                {{ $worker->ot_public_pay > 0 ? 'RM ' . number_format($worker->ot_public_pay, 2) : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="center" variant="strong" class="text-green-600 dark:text-green-400">
                                {{ number_format($worker->total_overtime_hours, 2) }}h
                            </flux:table.cell>

                            <flux:table.cell align="right" variant="strong" class="text-green-600 dark:text-green-400">
                                RM {{ number_format($worker->total_ot_pay, 2) }}
                            </flux:table.cell>
                        </flux:table.rows>
                        @endif
                    @endforeach

                    <!-- Summary Row -->
                    <flux:table.rows class="border-t-2 border-zinc-300 dark:border-zinc-600 bg-green-50 dark:bg-green-900/20">
                        <flux:table.cell colspan="2" variant="strong" class="font-bold">
                            TOTAL PAID THIS MONTH
                        </flux:table.cell>
                        <flux:table.cell align="center" variant="strong" class="font-bold">
                            {{ number_format($previousOtStats['total_weekday_ot_hours'], 2) }}h
                        </flux:table.cell>
                        <flux:table.cell align="right" variant="strong" class="font-bold">
                            RM {{ number_format($previousOtStats['total_weekday_ot_pay'], 2) }}
                        </flux:table.cell>
                        <flux:table.cell align="center" variant="strong" class="font-bold">
                            {{ number_format($previousOtStats['total_rest_ot_hours'], 2) }}h
                        </flux:table.cell>
                        <flux:table.cell align="right" variant="strong" class="font-bold">
                            RM {{ number_format($previousOtStats['total_rest_ot_pay'], 2) }}
                        </flux:table.cell>
                        <flux:table.cell align="center" variant="strong" class="font-bold">
                            {{ number_format($previousOtStats['total_public_ot_hours'], 2) }}h
                        </flux:table.cell>
                        <flux:table.cell align="right" variant="strong" class="font-bold">
                            RM {{ number_format($previousOtStats['total_public_ot_pay'], 2) }}
                        </flux:table.cell>
                        <flux:table.cell align="center" variant="strong" class="font-bold text-green-600 dark:text-green-400">
                            {{ number_format($previousOtStats['total_ot_hours'], 2) }}h
                        </flux:table.cell>
                        <flux:table.cell align="right" variant="strong" class="font-bold text-green-600 dark:text-green-400">
                            RM {{ number_format($previousOtStats['total_ot_pay'], 2) }}
                        </flux:table.cell>
                    </flux:table.rows>
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>
    @endif

    <!-- Current Month Deferred OT Breakdown by Worker -->
    <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Current Month OT Breakdown <span class="text-zinc-600 dark:text-zinc-400">(To be paid next month)</span></h2>
            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">This OT was calculated for {{ $submission->month_year }} and will be included in next month's payment</p>
        </div>

        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">No</span>
                    </flux:table.column>
                    <flux:table.column>
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Worker Name</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Weekday OT</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Weekday Pay</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Rest Day OT</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Rest Day Pay</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Public Holiday OT</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Public Holiday Pay</span>
                    </flux:table.column>
                    <flux:table.column align="center">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Hours</span>
                    </flux:table.column>
                    <flux:table.column align="right">
                        <span class="text-xs font-medium text-zinc-600 dark:text-zinc-400">Total OT Pay</span>
                    </flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($workers as $index => $worker)
                        @if($worker->total_overtime_hours > 0)
                        <flux:table.rows :key="$worker->id">
                            <flux:table.cell align="center">{{ $index + 1 }}</flux:table.cell>

                            <flux:table.cell variant="strong">
                                {{ $worker->worker_name }}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                {{ $worker->ot_normal_hours > 0 ? number_format($worker->ot_normal_hours, 2) . 'h' : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                {{ $worker->ot_normal_pay > 0 ? 'RM ' . number_format($worker->ot_normal_pay, 2) : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                {{ $worker->ot_rest_hours > 0 ? number_format($worker->ot_rest_hours, 2) . 'h' : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                {{ $worker->ot_rest_pay > 0 ? 'RM ' . number_format($worker->ot_rest_pay, 2) : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="center">
                                {{ $worker->ot_public_hours > 0 ? number_format($worker->ot_public_hours, 2) . 'h' : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="right">
                                {{ $worker->ot_public_pay > 0 ? 'RM ' . number_format($worker->ot_public_pay, 2) : '-' }}
                            </flux:table.cell>

                            <flux:table.cell align="center" variant="strong" class="">
                                {{ number_format($worker->total_overtime_hours, 2) }}h
                            </flux:table.cell>

                            <flux:table.cell align="right" variant="strong" class="">
                                RM {{ number_format($worker->total_ot_pay, 2) }}
                            </flux:table.cell>
                        </flux:table.rows>
                        @endif
                    @endforeach

                    @if($stats['total_ot_hours'] == 0)
                        <flux:table.rows>
                            <flux:table.cell colspan="10" class="text-center text-zinc-600 dark:text-zinc-400">
                                No overtime recorded for this month
                            </flux:table.cell>
                        </flux:table.rows>
                    @else
                        <!-- Summary Row -->
                        <flux:table.rows class="border-t-2 border-zinc-300 dark:border-zinc-600 bg-orange-50 dark:bg-orange-900/20">
                            <flux:table.cell colspan="2" variant="strong" class="font-bold">
                                <div class="ms-2">TOTAL DEFERRED OT</div>
                            </flux:table.cell>
                            <flux:table.cell align="center" variant="strong" class="font-bold">
                                {{ number_format($workers->sum('ot_normal_hours'), 2) }}h
                            </flux:table.cell>
                            <flux:table.cell align="right" variant="strong" class="font-bold">
                                RM {{ number_format($workers->sum('ot_normal_pay'), 2) }}
                            </flux:table.cell>
                            <flux:table.cell align="center" variant="strong" class="font-bold">
                                {{ number_format($workers->sum('ot_rest_hours'), 2) }}h
                            </flux:table.cell>
                            <flux:table.cell align="right" variant="strong" class="font-bold">
                                RM {{ number_format($workers->sum('ot_rest_pay'), 2) }}
                            </flux:table.cell>
                            <flux:table.cell align="center" variant="strong" class="font-bold">
                                {{ number_format($workers->sum('ot_public_hours'), 2) }}h
                            </flux:table.cell>
                            <flux:table.cell align="right" variant="strong" class="font-bold">
                                RM {{ number_format($workers->sum('ot_public_pay'), 2) }}
                            </flux:table.cell>
                            <flux:table.cell align="center" variant="strong" class="font-bold ">
                                {{ number_format($stats['total_ot_hours'], 2) }}h
                            </flux:table.cell>
                            <flux:table.cell align="right" variant="strong" class="font-bold ">
                                RM {{ number_format($stats['total_ot_pay'], 2) }}
                            </flux:table.cell>
                        </flux:table.rows>
                    @endif
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>
</div>
