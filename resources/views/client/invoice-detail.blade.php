<x-layouts.app :title="__('Invoice Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Invoice #INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $invoice->month_year }} Payroll Invoice</p>
            </div>
            <div class="flex gap-2">
                <flux:button variant="outline" href="{{ route('client.invoices.download', $invoice->id) }}">
                    <flux:icon.arrow-down-tray class="size-4" />
                    Download PDF
                </flux:button>
                <flux:button variant="outline" href="{{ route('client.invoices') }}">
                    <flux:icon.arrow-left class="size-4" />
                    Back to Invoices
                </flux:button>
            </div>
        </div>

        <!-- Invoice Info Card -->
        <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
            <div class="grid gap-6 md:grid-cols-2">
                <div>
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Invoice Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Invoice Number:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">INV-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Period:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $invoice->month_year }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Issue Date:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $invoice->submitted_at ? $invoice->submitted_at->format('F d, Y') : 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Due Date:</span>
                            <span class="font-medium {{ now()->gt($invoice->payment_deadline) && $invoice->status !== 'paid' ? 'text-red-600 dark:text-red-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                                {{ $invoice->payment_deadline->format('F d, Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-3">Payment Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Status:</span>
                            <div>
                                @if($invoice->status === 'draft')
                                    <flux:badge color="zinc" size="sm">Draft</flux:badge>
                                @elseif($invoice->status === 'pending_payment')
                                    <flux:badge color="yellow" size="sm">Pending Payment</flux:badge>
                                @elseif($invoice->status === 'paid')
                                    <flux:badge color="green" size="sm">Paid</flux:badge>
                                @elseif($invoice->status === 'overdue')
                                    <flux:badge color="red" size="sm">Overdue</flux:badge>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Total Workers:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $invoice->total_workers }}</span>
                        </div>
                        @if($invoice->payment)
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Payment Method:</span>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ ucfirst($invoice->payment->payment_method) }}</span>
                        </div>
                        @if($invoice->payment->transaction_id)
                        <div class="flex justify-between text-sm">
                            <span class="text-zinc-600 dark:text-zinc-400">Transaction ID:</span>
                            <span class="font-mono text-xs text-zinc-900 dark:text-zinc-100">{{ $invoice->payment->transaction_id }}</span>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
        </flux:card>

        <!-- Workers Breakdown -->
        <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Worker Salary Breakdown</h3>

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
                        @foreach($invoice->workers as $worker)
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
                                RM {{ number_format($invoice->total_amount, 2) }}
                            </td>
                        </tr>
                        @if($invoice->has_penalty)
                        <tr>
                            <td colspan="9" class="py-2 text-right text-sm font-semibold text-red-600 dark:text-red-400">
                                Late Payment Penalty (8%):
                            </td>
                            <td class="py-2 text-right text-sm font-semibold text-red-600 dark:text-red-400">
                                +RM {{ number_format($invoice->penalty_amount, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9" class="py-2 text-right text-base font-bold text-zinc-900 dark:text-zinc-100">
                                Total Amount Due:
                            </td>
                            <td class="py-2 text-right text-xl font-bold text-red-600 dark:text-red-400">
                                RM {{ number_format($invoice->total_with_penalty, 2) }}
                            </td>
                        </tr>
                        @else
                        <tr>
                            <td colspan="9" class="py-2 text-right text-base font-bold text-zinc-900 dark:text-zinc-100">
                                Total Amount Due:
                            </td>
                            <td class="py-2 text-right text-xl font-bold text-zinc-900 dark:text-zinc-100">
                                RM {{ number_format($invoice->total_amount, 2) }}
                            </td>
                        </tr>
                        @endif
                    </tfoot>
                </table>
            </div>
        </flux:card>
        
        <!-- Payment Action -->
        @if($invoice->status === 'pending_payment' || $invoice->status === 'overdue')
            <flux:card class="p-6 dark:bg-zinc-900 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Ready to Pay?</h3>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                            Complete your payment securely via Billplz using Online Banking (FPX)
                        </p>
                    </div>
                    <form method="POST" action="{{ route('client.payment.create', $invoice->id) }}">
                        @csrf
                        <flux:button type="submit" variant="primary">
                            <flux:icon.credit-card class="size-5 inline me-1" />
                            Pay with Online Banking
                        </flux:button>
                    </form>
                </div>
            </flux:card>
        @elseif($invoice->status === 'paid')
            <flux:card class="p-6 dark:bg-zinc-900 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                <div class="flex gap-3">
                    <flux:icon.check-circle class="size-6 flex-shrink-0 text-green-600 dark:text-green-400" />
                    <div>
                        <h3 class="text-lg font-semibold text-green-900 dark:text-green-100">Invoice Paid</h3>
                        <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                            This invoice was paid on {{ $invoice->payment->completed_at?->format('F d, Y h:i A') }}
                            @if($invoice->payment->transaction_id)
                                <br>Transaction ID: {{ $invoice->payment->transaction_id }}
                            @endif
                        </p>
                    </div>
                </div>
            </flux:card>
        @endif

        <!-- Payment Method Notice -->
        @if($invoice->status === 'pending_payment' || $invoice->status === 'overdue')
            <flux:card class="p-4 dark:bg-zinc-900 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
                <div class="flex gap-3">
                    <flux:icon.information-circle class="size-5 flex-shrink-0 text-amber-600 dark:text-amber-400" />
                    <div class="text-sm text-amber-900 dark:text-amber-100">
                        <p class="font-medium">Payment Method Information</p>
                        <p class="text-xs text-amber-700 dark:text-amber-300 mt-1">
                            Payment can only be made using <span class="font-semibold">Online Banking (FPX)</span> through our secure <span class="font-semibold">Billplz payment gateway</span>.
                            Credit/debit card payments are not accepted. Please ensure your online banking is activated before proceeding.
                        </p>
                    </div>
                </div>
            </flux:card>
        @endif

        <!-- OT Information Notice -->
        <flux:card class="p-4 dark:bg-zinc-900 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
            <div class="flex gap-3">
                <flux:icon.information-circle class="size-5 flex-shrink-0 text-blue-600 dark:text-blue-400" />
                <div class="text-sm text-blue-900 dark:text-blue-100">
                    <p class="font-medium">Important: Deferred OT Payment</p>
                    <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                        The overtime hours shown above are recorded for {{ $invoice->month_year }}, but they will be paid in the following month's payroll.
                        This month's payment includes basic salary plus previous month's overtime.
                    </p>
                </div>
            </div>
        </flux:card>

    </div>
</x-layouts.app>
