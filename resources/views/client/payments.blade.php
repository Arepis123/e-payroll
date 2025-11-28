<x-layouts.app :title="__('Payment History')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Payment History</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">View your payment records and transaction history</p>
            </div>
        </div>

        @if(session('error') || isset($error))
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-800">
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') ?? $error }}</p>
            </div>
        @endif

        @if(!isset($error))
        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-4">
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">This Month</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            RM {{ number_format($stats['this_month_amount'], 2) }}
                        </p>
                    </div>
                    <div class="rounded-full bg-purple-100 dark:bg-purple-900/30 p-3">
                        <flux:icon.wallet class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                @if($stats['this_month_status'] === 'paid')
                    <flux:badge color="green" size="sm">Paid</flux:badge>
                @elseif($stats['this_month_status'] === 'pending_payment')
                    <flux:badge color="orange" size="sm">Pending</flux:badge>
                @elseif($stats['this_month_status'] === 'overdue')
                    <flux:badge color="red" size="sm">Overdue</flux:badge>
                @else
                    <flux:badge color="zinc" size="sm">No Data</flux:badge>
                @endif
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Last Month</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            RM {{ number_format($stats['last_month_amount'], 2) }}
                        </p>
                    </div>
                    <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3">
                        <flux:icon.check-circle class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                @if($stats['last_month_amount'] > 0)
                    <flux:badge color="green" size="sm">Paid</flux:badge>
                @else
                    <flux:badge color="zinc" size="sm">No Data</flux:badge>
                @endif
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">This Year</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            RM {{ number_format($stats['this_year_amount'], 2) }}
                        </p>
                    </div>
                    <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-3">
                        <flux:icon.chart-bar class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $stats['this_year_count'] }} payments</p>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Avg Monthly</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">
                            RM {{ number_format($stats['avg_monthly'], 2) }}
                        </p>
                    </div>
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900/30 p-3">
                        <flux:icon.calculator class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">Based on {{ now()->year }}</p>
            </flux:card>
        </div>

        <!-- Payment History Table -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">All Payments</h2>
                <div class="flex gap-2">
                    <form method="GET" action="{{ route('payments') }}" id="yearFilterForm">
                        <flux:select variant="listbox" name="year" placeholder="Select year..." size="sm" class="w-auto">
                            @foreach($availableYears as $year)
                                <flux:select.option value="{{ $year }}" selected="{{ $year == $selectedYear }}">{{ $year }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </form>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const yearSelect = document.querySelector('select[name="year"]');
                        if (yearSelect) {
                            yearSelect.addEventListener('change', function() {
                                document.getElementById('yearFilterForm').submit();
                            });
                        }
                    });
                </script>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Transaction ID</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Period</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Amount</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Workers</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Payment Date</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Method</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($payments as $payment)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-mono text-zinc-900 dark:text-zinc-100">
                                {{ $payment->transaction_id ?? $payment->billplz_bill_id ?? 'N/A' }}
                            </td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $payment->submission->month_year }}
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                RM {{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $payment->submission->total_workers }} workers
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $payment->completed_at ? $payment->completed_at->format('M d, Y') : 'Pending' }}
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ ucfirst($payment->payment_method) }}
                            </td>
                            <td class="py-3">
                                @if($payment->status === 'completed')
                                    <flux:badge color="green" size="sm">Paid</flux:badge>
                                @elseif($payment->status === 'pending')
                                    <flux:badge color="orange" size="sm">Pending</flux:badge>
                                @elseif($payment->status === 'failed')
                                    <flux:badge color="red" size="sm">Failed</flux:badge>
                                @endif
                            </td>
                            <td class="py-3">
                                <flux:button
                                    variant="ghost"
                                    size="sm"
                                    icon="eye"
                                    href="{{ route('client.timesheet.show', $payment->submission->id) }}"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-zinc-600 dark:text-zinc-400">
                                No payment records found for {{ $selectedYear }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($payments->hasPages())
            <div class="mt-4 border-t border-zinc-200 dark:border-zinc-700 pt-4">
                {{ $payments->links() }}
            </div>
            @endif
        </flux:card>
        @endif
    </div>
</x-layouts.app>
