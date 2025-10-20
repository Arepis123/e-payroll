<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Dashboard</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Welcome back, {{ auth()->user()->company_name ?? auth()->user()->name }}</p>
            </div>
            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Total Workers -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">My Workers</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_workers'] }}</p>
                    </div>
                    <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-3">
                        <flux:icon.users class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-green-600 dark:text-green-400">{{ $stats['active_workers'] }} Active</span>
                    @if($stats['expiring_soon'] > 0)
                        <span class="text-zinc-600 dark:text-zinc-400">•</span>
                        <span class="text-orange-600 dark:text-orange-400">{{ $stats['expiring_soon'] }} Expiring Soon</span>
                    @endif
                </div>
            </flux:card>

            <!-- This Month Payment -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">This Month</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM {{ number_format($paymentStats['this_month_amount'], 2) }}</p>
                    </div>
                    <div class="rounded-full bg-purple-100 dark:bg-purple-900/30 p-3">
                        <flux:icon.wallet class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    @if($paymentStats['this_month_deadline'])
                        <span class="text-zinc-600 dark:text-zinc-400">Payment deadline: {{ $paymentStats['this_month_deadline']->format('M d') }}</span>
                    @else
                        <span class="text-zinc-600 dark:text-zinc-400">No submission for this month</span>
                    @endif
                </div>
            </flux:card>

            <!-- Outstanding Balance -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Outstanding Balance</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM {{ number_format($paymentStats['outstanding_balance'], 2) }}</p>
                    </div>
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900/30 p-3">
                        <flux:icon.exclamation-circle class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    @if($paymentStats['outstanding_balance'] > 0)
                        <span class="text-orange-600 dark:text-orange-400">Pending & unpaid</span>
                    @else
                        <span class="text-green-600 dark:text-green-400">All paid up</span>
                    @endif
                </div>
            </flux:card>

            <!-- Paid This Year -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Paid This Year</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM {{ number_format($paymentStats['year_to_date_paid'], 2) }}</p>
                    </div>
                    <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3">
                        <flux:icon.check-circle class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-zinc-600 dark:text-zinc-400">January - {{ now()->format('F') }} {{ now()->year }}</span>
                </div>
            </flux:card>
        </div>

        <!-- Main Content -->
        <div class="grid gap-4 lg:grid-cols-3">
            <!-- Recent Workers & Payments -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Recent Workers -->
                <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">My Workers</h2>
                        <flux:button variant="ghost" size="sm" href="{{ route('client.workers') }}" wire:navigate>View all</flux:button>
                    </div>

                    <div class="space-y-3">
                        @forelse($recentWorkers as $worker)
                            <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="{{ $worker->name }}" />
                                    <div>
                                        <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->name }}</p>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400">
                                            {{ $worker->position ?? 'Worker' }} • {{ $worker->ic_number }}
                                        </p>
                                        @if($worker->contract_info)
                                            <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                                Contract: {{ $worker->contract_info->con_start->format('M d, Y') }} - {{ $worker->contract_info->con_end->format('M d, Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                @if($worker->contract_info && $worker->contract_info->isActive())
                                    <flux:badge color="green" size="sm">Active</flux:badge>
                                @else
                                    <flux:badge color="zinc" size="sm">Inactive</flux:badge>
                                @endif
                            </div>
                        @empty
                            <div class="p-6 text-center text-zinc-600 dark:text-zinc-400">
                                <p>No workers found.</p>
                            </div>
                        @endforelse
                    </div>
                </flux:card>

                <!-- Payment History -->
                <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Payment History</h2>
                        <flux:button variant="ghost" size="sm" href="{{ route('client.payments') }}" wire:navigate>View all</flux:button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                    <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Month</th>
                                    <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Amount</th>
                                    <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Workers</th>
                                    <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                @forelse($recentPayments as $payment)
                                    <tr>
                                        <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $payment->month_year }}</td>
                                        <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                            RM {{ number_format($payment->total_with_penalty, 2) }}
                                            @if($payment->has_penalty)
                                                <span class="text-xs text-orange-600 dark:text-orange-400">(+8% penalty)</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $payment->total_workers }} {{ Str::plural('worker', $payment->total_workers) }}</td>
                                        <td class="py-3">
                                            @if($payment->status === 'paid')
                                                <flux:badge color="green" size="sm">Paid</flux:badge>
                                            @elseif($payment->status === 'pending_payment')
                                                <flux:badge color="yellow" size="sm">Pending Payment</flux:badge>
                                            @elseif($payment->status === 'overdue')
                                                <flux:badge color="red" size="sm">Overdue</flux:badge>
                                            @else
                                                <flux:badge color="zinc" size="sm">{{ ucfirst($payment->status) }}</flux:badge>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-6 text-center text-sm text-zinc-600 dark:text-zinc-400">
                                            No payment history available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </flux:card>
            </div>

            <!-- Sidebar: Quick Actions & Notifications -->
            <div class="space-y-4">
                <!-- Quick Actions -->
                <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Quick Actions</h2>
                    <div class="space-y-2">
                        <flux:button variant="primary" class="w-full" href="{{ route('client.timesheet') }}" wire:navigate>
                            <flux:icon.calendar class="size-4" />
                            Submit Timesheet
                        </flux:button>
                        <flux:button variant="outline" class="w-full" href="{{ route('client.workers') }}" wire:navigate>
                            <flux:icon.users class="size-4" />
                            View Workers
                        </flux:button>
                        <flux:button variant="outline" class="w-full" href="{{ route('client.invoices') }}" wire:navigate>
                            <flux:icon.document-text class="size-4" />
                            View Invoices
                        </flux:button>
                    </div>
                </flux:card>

                <!-- Notifications -->
                <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Notifications</h2>
                    <div class="space-y-3">
                        @php
                            $hasNotifications = false;
                        @endphp

                        @if($paymentStats['this_month_deadline'] && $paymentStats['this_month_status'] !== 'paid' && $paymentStats['this_month_deadline']->isAfter(now()) && $paymentStats['this_month_deadline']->diffInDays(now()) <= 7)
                            @php $hasNotifications = true; @endphp
                            <div class="flex gap-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 p-3">
                                <flux:icon.exclamation-triangle class="size-5 flex-shrink-0 text-orange-600 dark:text-orange-400" />
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Payment Deadline Approaching</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">Payment due {{ $paymentStats['this_month_deadline']->format('M d, Y') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($paymentStats['unsubmitted_workers'] > 0)
                            @php $hasNotifications = true; @endphp
                            <div class="flex gap-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 p-3">
                                <flux:icon.exclamation-circle class="size-5 flex-shrink-0 text-blue-600 dark:text-blue-400" />
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Timesheet Needed</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $paymentStats['unsubmitted_workers'] }} {{ Str::plural('worker', $paymentStats['unsubmitted_workers']) }} need timesheet submission this month</p>
                                </div>
                            </div>
                        @endif

                        @if($stats['expiring_soon'] > 0)
                            @php $hasNotifications = true; @endphp
                            <div class="flex gap-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 p-3">
                                <flux:icon.exclamation-triangle class="size-5 flex-shrink-0 text-orange-600 dark:text-orange-400" />
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Contracts Expiring Soon</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">{{ $stats['expiring_soon'] }} {{ Str::plural('contract', $stats['expiring_soon']) }} expiring within 30 days</p>
                                </div>
                            </div>
                        @endif

                        @if($paymentStats['outstanding_balance'] > 0)
                            @php $hasNotifications = true; @endphp
                            <div class="flex gap-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 p-3">
                                <flux:icon.exclamation-circle class="size-5 flex-shrink-0 text-orange-600 dark:text-orange-400" />
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Outstanding Balance</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">RM {{ number_format($paymentStats['outstanding_balance'], 2) }} pending payment</p>
                                </div>
                            </div>
                        @endif

                        @if(!$hasNotifications)
                            <div class="flex gap-3 rounded-lg bg-green-50 dark:bg-green-900/20 p-3">
                                <flux:icon.check-circle class="size-5 flex-shrink-0 text-green-600 dark:text-green-400" />
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">All Caught Up</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">No pending notifications</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </flux:card>

                <!-- Company Info -->
                <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Company Info</h2>
                    <div class="space-y-2 text-sm">
                        <div>
                            <p class="text-zinc-600 dark:text-zinc-400">Company Name</p>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ auth()->user()->company_name ?? 'Not set' }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-600 dark:text-zinc-400">Contact Person</p>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ auth()->user()->name }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-600 dark:text-zinc-400">Email</p>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <p class="text-zinc-600 dark:text-zinc-400">Phone</p>
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ auth()->user()->phone ?? 'Not set' }}</p>
                        </div>
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
</x-layouts.app>
