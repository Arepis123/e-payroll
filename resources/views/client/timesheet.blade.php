<x-layouts.app :title="__('Timesheet')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Timesheet Management</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Submit worker attendance and hours</p>
            </div>
            <flux:button variant="primary" href="#">
                <flux:icon.plus class="size-4" />
                Submit Timesheet
            </flux:button>
        </div>

        <!-- Current Month Info -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ now()->format('F Y') }} Timesheet</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                        Submission deadline: <span class="font-semibold text-orange-600 dark:text-orange-400">{{ now()->format('F 20, Y') }}</span>
                    </p>
                </div>
                <flux:badge color="orange" size="lg">Pending Submission</flux:badge>
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
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Working Days</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">22</p>
                    </div>
                    <flux:icon.calendar class="size-8 text-green-600 dark:text-green-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Hours</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">2,640</p>
                    </div>
                    <flux:icon.clock class="size-8 text-purple-600 dark:text-purple-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Overtime Hours</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">120</p>
                    </div>
                    <flux:icon.fire class="size-8 text-orange-600 dark:text-orange-400" />
                </div>
            </flux:card>
        </div>

        <!-- Timesheet Entry Form -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Worker Attendance & Hours</h2>

            <!-- Month Selector -->
            <div class="mb-6 flex items-center gap-4">
                <flux:select class="w-48">
                    <flux:select.option>January 2025</flux:select.option>
                    <flux:select.option>December 2024</flux:select.option>
                    <flux:select.option>November 2024</flux:select.option>
                </flux:select>
                <flux:button variant="outline" size="sm">
                    <flux:icon.arrow-path class="size-4" />
                    Load Data
                </flux:button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Worker Name</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Employee ID</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Days Worked</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Regular Hours</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Overtime Hours</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Leave Days</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Jefri Aldi Kurniawan" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Jefri Aldi Kurniawan</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP001</td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="22" min="0" max="31" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-24" value="176" min="0" step="0.5" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="8" min="0" step="0.5" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="0" min="0" />
                            </td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Complete</flux:badge>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Siti Nurhaliza" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Siti Nurhaliza</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP002</td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="22" min="0" max="31" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-24" value="176" min="0" step="0.5" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="0" min="0" step="0.5" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="0" min="0" />
                            </td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Complete</flux:badge>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Ghulam Abbas" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Ghulam Abbas</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP005</td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="20" min="0" max="31" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-24" value="160" min="0" step="0.5" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="4" min="0" step="0.5" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="2" min="0" />
                            </td>
                            <td class="py-3">
                                <flux:badge color="yellow" size="sm">Pending</flux:badge>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Heri Siswanto" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Heri Siswanto</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP006</td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="22" min="0" max="31" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-24" value="176" min="0" step="0.5" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="12" min="0" step="0.5" />
                            </td>
                            <td class="py-3">
                                <flux:input type="number" class="w-20" value="0" min="0" />
                            </td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Complete</flux:badge>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-between items-center">
                <div class="flex gap-2">
                    <flux:button variant="outline">
                        <flux:icon.arrow-down-tray class="size-4" />
                        Import from Excel
                    </flux:button>
                    <flux:button variant="ghost">
                        <flux:icon.arrow-path class="size-4" />
                        Reset
                    </flux:button>
                </div>
                <div class="flex gap-2">
                    <flux:button variant="outline">
                        Save Draft
                    </flux:button>
                    <flux:button variant="primary">
                        <flux:icon.check class="size-4" />
                        Submit Timesheet
                    </flux:button>
                </div>
            </div>
        </flux:card>

        <!-- Submission History -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Submission History</h2>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Month</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Submitted Date</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Workers</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Hours</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">January 2025</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Not submitted</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">15</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">-</td>
                            <td class="py-3">
                                <flux:badge color="orange" size="sm">Pending</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:button variant="ghost" size="sm">Edit</flux:button>
                            </td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">December 2024</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Dec 15, 2024</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">11</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">1,936 hrs</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Approved</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:button variant="ghost" size="sm" icon="eye">View</flux:button>
                            </td>
                        </tr>
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">November 2024</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Nov 18, 2024</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">10</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">1,760 hrs</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Approved</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:button variant="ghost" size="sm" icon="eye">View</flux:button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
