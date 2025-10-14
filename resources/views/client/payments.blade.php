<x-layouts.app :title="__('Payment History')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Payment History</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">View your payment records and history</p>
            </div>
            <flux:button variant="outline" href="#">
                <flux:icon.arrow-down-tray class="size-4" />
                Export Report
            </flux:button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-4">
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
                <flux:badge color="yellow" size="sm">Pending</flux:badge>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Last Month</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 42,100</p>
                    </div>
                    <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3">
                        <flux:icon.check-circle class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <flux:badge color="green" size="sm">Paid</flux:badge>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">This Year</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 486,250</p>
                    </div>
                    <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-3">
                        <flux:icon.chart-bar class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">12 payments</p>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Avg Monthly</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 40,521</p>
                    </div>
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900/30 p-3">
                        <flux:icon.calculator class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">Based on 2025</p>
            </flux:card>
        </div>

        <!-- Payment History Table -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">All Payments</h2>
                <div class="flex gap-2">
                    <flux:select class="w-40">
                        <flux:select.option>2025</flux:select.option>
                        <flux:select.option>2024</flux:select.option>
                        <flux:select.option>2023</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Payment ID</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Month</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Amount</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Workers</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Payment Date</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY-0125</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">January 2025</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 45,200</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">12 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Pending</td>
                            <td class="py-3">
                                <flux:badge color="yellow" size="sm">Pending</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:button variant="ghost" size="sm" icon="eye" />
                            </td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY-1224</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">December 2024</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 42,100</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">11 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Dec 28, 2024</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:button variant="ghost" size="sm" icon="eye" />
                            </td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY-1124</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">November 2024</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 38,900</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">10 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Nov 25, 2024</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:button variant="ghost" size="sm" icon="eye" />
                            </td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY-1024</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">October 2024</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 39,500</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">10 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Oct 28, 2024</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:button variant="ghost" size="sm" icon="eye" />
                            </td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY-0924</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">September 2024</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 41,200</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">11 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Sep 27, 2024</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:button variant="ghost" size="sm" icon="eye" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-700 pt-4">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Showing 1 to 5 of 12 results
                </p>
                <div class="flex gap-2">
                    <flux:button variant="ghost" size="sm" disabled>Previous</flux:button>
                    <flux:button variant="ghost" size="sm">1</flux:button>
                    <flux:button variant="outline" size="sm">2</flux:button>
                    <flux:button variant="ghost" size="sm">3</flux:button>
                    <flux:button variant="ghost" size="sm">Next</flux:button>
                </div>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
