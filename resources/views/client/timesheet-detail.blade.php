<x-layouts.app :title="__('Payroll Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Payroll Details</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $submission->month_year }}</p>
            </div>
            <div class="flex gap-2">
                @if($submission->status === 'draft')
                    <flux:button variant="primary" icon="pencil" href="{{ route('client.timesheet.edit', $submission->id) }}">
                        Edit Draft
                    </flux:button>
                @endif
                <flux:button variant="outline" href="{{ route('client.timesheet') }}">
                    <flux:icon.arrow-left class="size-4 inline" />
                    Back to Timesheet
                </flux:button>
            </div>
        </div>

        <!-- Submission Info Card -->
        <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Period</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $submission->month_year }}</p>
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Submitted Date</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">
                        {{ $submission->submitted_at ? $submission->submitted_at->format('F d, Y h:i A') : 'Not submitted yet' }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Status</p>
                    <div class="mt-1">
                        @if($submission->status === 'draft')
                            <flux:badge color="zinc" >Draft</flux:badge>
                        @elseif($submission->status === 'pending_payment')
                            <flux:badge color="orange" >Pending Payment</flux:badge>
                        @elseif($submission->status === 'paid')
                            <flux:badge color="green" >Paid</flux:badge>
                        @elseif($submission->status === 'overdue')
                            <flux:badge color="red" >Overdue</flux:badge>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Workers</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $submission->total_workers }}</p>
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Payment Deadline</p>
                    <p class="text-lg font-semibold {{ now()->gt($submission->payment_deadline) ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                        {{ $submission->payment_deadline->format('F d, Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Amount</p>
                    <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">RM {{ number_format($submission->total_amount, 2) }}</p>
                    @if($submission->has_penalty)
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                            + Penalty: RM {{ number_format($submission->penalty_amount, 2) }}
                        </p>
                        <p class="text-sm font-bold text-red-600 dark:text-red-400">
                            Total with penalty: RM {{ number_format($submission->total_with_penalty, 2) }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Payment Action -->
            @if($submission->status === 'pending_payment' || $submission->status === 'overdue')
                <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <form method="POST" action="{{ route('client.payment.create', $submission->id) }}">
                        @csrf
                        <flux:button type="submit" variant="primary" >
                            <flux:icon.credit-card class="size-5 inline me-1" />
                            Pay Now - RM {{ number_format($submission->has_penalty ? $submission->total_with_penalty : $submission->total_amount, 2) }}
                        </flux:button>
                    </form>
                </div>
            @elseif($submission->payment && $submission->status === 'paid')
                <div class="mt-6 pt-6 border-t border-zinc-200 dark:border-zinc-700">
                    <div class="rounded-lg bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800">
                        <div class="flex gap-3">
                            <flux:icon.check-circle class="size-5 flex-shrink-0 text-green-600 dark:text-green-400" />
                            <div class="text-sm text-green-900 dark:text-green-100">
                                <p class="font-medium">Payment Completed</p>
                                <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                    Paid on {{ $submission->payment->completed_at?->format('F d, Y h:i A') }}
                                    @if($submission->payment->transaction_id)
                                        | Transaction ID: {{ $submission->payment->transaction_id }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </flux:card>

        <!-- Workers Details -->
        <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Worker Breakdown</h2>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Worker</th>
                            <th class="pb-3 text-right text-xs font-medium text-zinc-600 dark:text-zinc-400">Basic Salary</th>
                            <th class="pb-3 text-right text-xs font-medium text-zinc-600 dark:text-zinc-400">OT Normal</th>
                            <th class="pb-3 text-right text-xs font-medium text-zinc-600 dark:text-zinc-400">OT Rest</th>
                            <th class="pb-3 text-right text-xs font-medium text-zinc-600 dark:text-zinc-400">OT Public</th>
                            <th class="pb-3 text-right text-xs font-medium text-zinc-600 dark:text-zinc-400">Transactions</th>
                            <th class="pb-3 text-right text-xs font-medium text-zinc-600 dark:text-zinc-400">Gross Salary</th>
                            <th class="pb-3 text-right text-xs font-medium text-zinc-600 dark:text-zinc-400">Deductions</th>
                            <th class="pb-3 text-right text-xs font-medium text-zinc-600 dark:text-zinc-400">Net Salary</th>
                            <th class="pb-3 text-right text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Payment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($submission->workers as $worker)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="{{ $worker->worker_name }}" />
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->worker_name }}</p>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $worker->worker_id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-right text-sm text-zinc-900 dark:text-zinc-100">
                                RM {{ number_format($worker->basic_salary, 2) }}
                            </td>
                            <td class="py-3 text-right text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $worker->ot_normal_hours }}h<br>
                                <span class="text-xs">RM {{ number_format($worker->ot_normal_pay, 2) }}</span>
                            </td>
                            <td class="py-3 text-right text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $worker->ot_rest_hours }}h<br>
                                <span class="text-xs">RM {{ number_format($worker->ot_rest_pay, 2) }}</span>
                            </td>
                            <td class="py-3 text-right text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $worker->ot_public_hours }}h<br>
                                <span class="text-xs">RM {{ number_format($worker->ot_public_pay, 2) }}</span>
                            </td>
                            <td class="py-3">
                                @php
                                    $workerTransactions = $worker->transactions ?? collect([]);
                                    $advancePayments = $workerTransactions->where('type', 'advance_payment');
                                    $deductions = $workerTransactions->where('type', 'deduction');
                                @endphp
                                @if($workerTransactions->count() > 0)
                                    <div class="space-y-1">
                                        @if($advancePayments->count() > 0)
                                            <div class="text-xs text-right">
                                                <span class="font-medium text-green-600 dark:text-green-400">Advance Payment:</span>
                                                @foreach($advancePayments as $transaction)
                                                    <div class="ml-2 text-zinc-600 dark:text-zinc-400">
                                                        • RM {{ number_format($transaction->amount, 2) }}
                                                        <span class="text-xs italic">({{ $transaction->remarks }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($deductions->count() > 0)
                                            <div class="text-xs text-right">
                                                <span class="font-medium text-red-600 dark:text-red-400">Deduction:</span>
                                                @foreach($deductions as $transaction)
                                                    <div class="ml-2 text-zinc-600 dark:text-zinc-400">
                                                        • RM {{ number_format($transaction->amount, 2) }}
                                                        <span class="text-xs italic">({{ $transaction->remarks }})</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400 dark:text-zinc-500">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-right text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                RM {{ number_format($worker->gross_salary, 2) }}
                            </td>
                            <td class="py-3 text-right text-sm text-red-600 dark:text-red-400">
                                -RM {{ number_format($worker->total_deductions, 2) }}<br>
                                <span class="text-xs">(EPF+SOCSO)</span>
                            </td>
                            <td class="py-3 text-right text-sm font-medium text-green-600 dark:text-green-400">
                                RM {{ number_format($worker->net_salary, 2) }}
                            </td>
                            <td class="py-3 text-right text-sm font-bold text-zinc-900 dark:text-zinc-100">
                                RM {{ number_format($worker->total_payment, 2) }}<br>
                                <span class="text-xs text-zinc-600 dark:text-zinc-400">
                                    (+RM {{ number_format($worker->total_employer_contribution, 2) }} contrib.)
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-zinc-300 dark:border-zinc-600">
                            <td colspan="9" class="py-4 text-right text-sm font-bold text-zinc-900 dark:text-zinc-100">
                                Total Amount:
                            </td>
                            <td class="py-4 text-right text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                RM {{ number_format($submission->total_amount, 2) }}
                            </td>
                        </tr>
                        @if($submission->has_penalty)
                        <tr>
                            <td colspan="9" class="py-2 text-right text-sm font-semibold text-red-600 dark:text-red-400">
                                Late Payment Penalty (8%):
                            </td>
                            <td class="py-2 text-right text-sm font-semibold text-red-600 dark:text-red-400">
                                +RM {{ number_format($submission->penalty_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9" class="py-2 text-right text-sm font-bold text-zinc-900 dark:text-zinc-100">
                                Total with Penalty:
                            </td>
                            <td class="py-2 text-right text-lg font-bold text-red-600 dark:text-red-400">
                                RM {{ number_format($submission->total_with_penalty, 2) }}
                            </td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </flux:card>

        <!-- OT Information Notice -->
        <flux:card class="p-4 dark:bg-zinc-900 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <div class="flex gap-3">
                <flux:icon.information-circle class="size-5 flex-shrink-0 text-blue-600 dark:text-blue-400" />
                <div class="text-sm text-blue-900 dark:text-blue-100">
                    <p class="font-medium">Important: Deferred OT Payment</p>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                        The overtime hours shown above are recorded for {{ $submission->month_year }}, but they will be paid in the following month's payroll.
                        This month's payment includes basic salary plus previous month's overtime.
                    </p>
                </div>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
