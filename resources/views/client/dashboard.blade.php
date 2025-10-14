<x-layouts.app :title="__('Client Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Client Dashboard</h1>
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
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">15</p>
                    </div>
                    <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-3">
                        <flux:icon.users class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-green-600 dark:text-green-400">12 Active</span>
                    <span class="text-zinc-600 dark:text-zinc-400">•</span>
                    <span class="text-orange-600 dark:text-orange-400">3 On Leave</span>
                </div>
            </flux:card>

            <!-- This Month Payment -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">This Month</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 45,200</p>
                    </div>
                    <div class="rounded-full bg-purple-100 dark:bg-purple-900/30 p-3">
                        <flux:icon.wallet class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-zinc-600 dark:text-zinc-400">Expected payment date: Jan 25</span>
                </div>
            </flux:card>

            <!-- Pending Approvals -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending Approvals</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">3</p>
                    </div>
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900/30 p-3">
                        <flux:icon.clock class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-orange-600 dark:text-orange-400">Requires your attention</span>
                </div>
            </flux:card>

            <!-- Paid This Year -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Paid This Year</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 486,250</p>
                    </div>
                    <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3">
                        <flux:icon.check-circle class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-zinc-600 dark:text-zinc-400">January - December 2025</span>
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
                        <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                            <div class="flex items-center gap-3">
                                <flux:avatar size="sm" name="Jefri Aldi Kurniawan" />
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Jefri Aldi Kurniawan</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">General Worker • EMP001</p>
                                </div>
                            </div>
                            <flux:badge color="green" size="sm">Active</flux:badge>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                            <div class="flex items-center gap-3">
                                <flux:avatar size="sm" name="Siti Nurhaliza" />
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Siti Nurhaliza</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">General Worker • EMP002</p>
                                </div>
                            </div>
                            <flux:badge color="green" size="sm">Active</flux:badge>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                            <div class="flex items-center gap-3">
                                <flux:avatar size="sm" name="Ghulam Abbas" />
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Ghulam Abbas</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">General Worker • EMP005</p>
                                </div>
                            </div>
                            <flux:badge color="green" size="sm">Active</flux:badge>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-lg bg-zinc-50 dark:bg-zinc-800/50">
                            <div class="flex items-center gap-3">
                                <flux:avatar size="sm" name="Heri Siswanto" />
                                <div>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Heri Siswanto</p>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">Carpenter • EMP006</p>
                                </div>
                            </div>
                            <flux:badge color="green" size="sm">Active</flux:badge>
                        </div>
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
                                <tr>
                                    <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">January 2025</td>
                                    <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 45,200</td>
                                    <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">12 workers</td>
                                    <td class="py-3">
                                        <flux:badge color="yellow" size="sm">Pending</flux:badge>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">December 2024</td>
                                    <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 42,100</td>
                                    <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">11 workers</td>
                                    <td class="py-3">
                                        <flux:badge color="green" size="sm">Paid</flux:badge>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">November 2024</td>
                                    <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 38,900</td>
                                    <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">10 workers</td>
                                    <td class="py-3">
                                        <flux:badge color="green" size="sm">Paid</flux:badge>
                                    </td>
                                </tr>
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
                        <div class="flex gap-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 p-3">
                            <flux:icon.exclamation-triangle class="size-5 flex-shrink-0 text-orange-600 dark:text-orange-400" />
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Timesheet Reminder</p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Submit January timesheet before 20th</p>
                            </div>
                        </div>

                        <div class="flex gap-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 p-3">
                            <flux:icon.information-circle class="size-5 flex-shrink-0 text-blue-600 dark:text-blue-400" />
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Payment Processed</p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">December payment completed</p>
                            </div>
                        </div>

                        <div class="flex gap-3 rounded-lg bg-green-50 dark:bg-green-900/20 p-3">
                            <flux:icon.check-circle class="size-5 flex-shrink-0 text-green-600 dark:text-green-400" />
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Invoice Generated</p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">January invoice is ready</p>
                            </div>
                        </div>
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
