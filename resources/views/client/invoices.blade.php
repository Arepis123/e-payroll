<x-layouts.app :title="__('Invoices')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Invoices</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">View and download your invoices</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-3">
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending Invoices</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">1</p>
                    </div>
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900/30 p-3">
                        <flux:icon.clock class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Paid Invoices</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">11</p>
                    </div>
                    <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3">
                        <flux:icon.check-circle class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Invoiced</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 486,250</p>
                    </div>
                    <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-3">
                        <flux:icon.document-text class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
            </flux:card>
        </div>

        <!-- Invoices Table -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">All Invoices</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Invoice #</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Month</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Amount</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Issue Date</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Due Date</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">INV-0125</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">January 2025</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 45,200</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Jan 1, 2025</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Jan 25, 2025</td>
                            <td class="py-3">
                                <flux:badge color="yellow" size="sm">Pending</flux:badge>
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <flux:button variant="ghost" size="sm" icon="eye" title="View" />
                                    <flux:button variant="ghost" size="sm" icon="arrow-down-tray" title="Download" />
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">INV-1224</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">December 2024</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 42,100</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Dec 1, 2024</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Dec 25, 2024</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <flux:button variant="ghost" size="sm" icon="eye" title="View" />
                                    <flux:button variant="ghost" size="sm" icon="arrow-down-tray" title="Download" />
                                </div>
                            </td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">INV-1124</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">November 2024</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 38,900</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Nov 1, 2024</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Nov 25, 2024</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    <flux:button variant="ghost" size="sm" icon="eye" title="View" />
                                    <flux:button variant="ghost" size="sm" icon="arrow-down-tray" title="Download" />
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
