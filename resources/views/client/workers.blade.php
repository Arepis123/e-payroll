<x-layouts.app :title="__('My Workers')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">My Workers</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Manage workers assigned to your company</p>
            </div>
        </div>

        <!-- Filters and Search -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="md:col-span-2">
                    <flux:input
                        placeholder="Search by name or employee ID..."
                        icon="magnifying-glass"
                    />
                </div>
                <div>
                    <flux:select placeholder="Filter by Status">
                        <flux:select.option>All Status</flux:select.option>
                        <flux:select.option>Active</flux:select.option>
                        <flux:select.option>On Leave</flux:select.option>
                    </flux:select>
                </div>
            </div>
        </flux:card>

        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-4">
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Workers</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">15</p>
                    </div>
                    <flux:icon.users class="size-8 text-blue-600 dark:text-blue-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Active</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">12</p>
                    </div>
                    <flux:icon.check-circle class="size-8 text-green-600 dark:text-green-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">On Leave</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">3</p>
                    </div>
                    <flux:icon.clock class="size-8 text-orange-600 dark:text-orange-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Avg Salary</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">RM 3,013</p>
                    </div>
                    <flux:icon.wallet class="size-8 text-purple-600 dark:text-purple-400" />
                </div>
            </flux:card>
        </div>

        <!-- Workers List -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">All Workers</h2>
                <div class="flex gap-2">
                    <flux:button variant="ghost" size="sm">
                        <flux:icon.arrow-down-tray class="size-4" />
                        Export
                    </flux:button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Employee ID</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">IC Number</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Position</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Basic Salary</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP001</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Jefri Aldi Kurniawan" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Jefri Aldi Kurniawan</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">920512-14-5678</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 3,500</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP002</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Siti Nurhaliza" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Siti Nurhaliza</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">880215-08-1234</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 1,800</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP005</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Ghulam Abbas" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Ghulam Abbas</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">950618-05-7890</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 2,200</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP006</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Heri Siswanto" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Heri Siswanto</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">870924-06-5555</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">Carpenter</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 2,500</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-700 pt-4">
                <p class="text-sm text-zinc-600 dark:text-zinc-400">
                    Showing 1 to 4 of 15 results
                </p>
                <div class="flex gap-2">
                    <flux:button variant="ghost" size="sm" disabled>Previous</flux:button>
                    <flux:button variant="ghost" size="sm">1</flux:button>
                    <flux:button variant="outline" size="sm">2</flux:button>
                    <flux:button variant="ghost" size="sm">Next</flux:button>
                </div>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
