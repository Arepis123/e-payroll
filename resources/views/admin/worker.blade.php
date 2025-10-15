<x-layouts.app :title="__('Worker Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Worker Management</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Manage all construction workers</p>
            </div>
            <flux:button variant="primary" href="#" wire:navigate>
                <flux:icon.plus class="size-4" />
                Add New Worker
            </flux:button>
        </div>

        <!-- Filters and Search -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="grid gap-4 md:grid-cols-4">
                <div class="md:col-span-2">
                    <flux:input
                        placeholder="Search by name, IC, or employee ID..."
                        icon="magnifying-glass"
                    />
                </div>
                <div>
                    <flux:select placeholder="Filter by Client">
                        <flux:select.option>All Clients</flux:select.option>
                        <flux:select.option>Miqabina Sdn Bhd</flux:select.option>
                        <flux:select.option>WCT Berhad</flux:select.option>
                        <flux:select.option>Chuan Luck Piling Sdn Bhd</flux:select.option>
                        <flux:select.option>Best Stone Sdn Bhd</flux:select.option>
                    </flux:select>
                </div>
                <div>
                    <flux:select placeholder="Filter by Status">
                        <flux:select.option>All Status</flux:select.option>
                        <flux:select.option>Active</flux:select.option>
                        <flux:select.option>Inactive</flux:select.option>
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
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">342</p>
                    </div>
                    <flux:icon.users class="size-8 text-blue-600 dark:text-blue-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Active</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">318</p>
                    </div>
                    <flux:icon.check-circle class="size-8 text-green-600 dark:text-green-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">On Leave</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">18</p>
                    </div>
                    <flux:icon.clock class="size-8 text-orange-600 dark:text-orange-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Inactive</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">6</p>
                    </div>
                    <flux:icon.x-circle class="size-8 text-red-600 dark:text-red-400" />
                </div>
            </flux:card>
        </div>

        <!-- Workers Table -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">All Workers</h2>
                <div class="flex gap-2">
                    <flux:button variant="ghost" size="sm">
                        <flux:icon.arrow-down-tray class="size-4" />
                        Export
                    </flux:button>
                    <flux:button variant="ghost" size="sm">
                        <flux:icon.funnel class="size-4" />
                        Filter
                    </flux:button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">
                                <flux:checkbox />
                            </th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Employee ID</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Passport Number</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Position</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Current Client</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Basic Salary</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <flux:checkbox />
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP001</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Jefri Aldi Kurniawan" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Jefri Aldi Kurniawan</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">920512-14-5678</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Miqabina</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 3,500</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Delete</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <flux:checkbox />
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP002</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Siti Nurhaliza" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Siti Nurhaliza</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">880215-08-1234</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Miqabina</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 1,800</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Delete</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <flux:checkbox />
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP003</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Chit Win Maung" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Chit Win Maung</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">850708-10-9876</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Chuan Luck Piling</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 2,800</td>
                            <td class="py-3">
                                <flux:badge color="orange" size="sm">On Leave</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Delete</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <flux:checkbox />
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP004</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Mojahidul Rohim" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Mojahidul Rohim</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">900322-07-4321</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Best Stone</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 4,200</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Delete</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <flux:checkbox />
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP005</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Ghulam Abbas" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Ghulam Abbas</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">950618-05-7890</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Miqabina</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 2,200</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Delete</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3">
                                <flux:checkbox />
                            </td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">EMP006</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Heri Siswanto" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Heri Siswanto</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">870924-06-5555</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">Carpenter</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Miqabina</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 2,500</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Active</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                        <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Delete</flux:menu.item>
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
                    Showing 1 to 6 of 342 results
                </p>
                <div class="flex gap-2">
                    <flux:button variant="ghost" size="sm" disabled>Previous</flux:button>
                    <flux:button variant="ghost" size="sm">1</flux:button>
                    <flux:button variant="outline" size="sm">2</flux:button>
                    <flux:button variant="ghost" size="sm">3</flux:button>
                    <flux:button variant="ghost" size="sm">...</flux:button>
                    <flux:button variant="ghost" size="sm">57</flux:button>
                    <flux:button variant="ghost" size="sm">Next</flux:button>
                </div>
            </div>
        </flux:card>
    </div>
</x-layouts.app>
