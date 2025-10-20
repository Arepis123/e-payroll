<div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Salary Submission</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Submit payroll and process payments</p>
            </div>
            <div class="flex gap-2">
                <flux:button variant="outline" href="#" wire:navigate class="flex-1 sm:flex-none">
                    <flux:icon.document-text class="size-4" />
                    <span class="hidden sm:inline">View History</span>
                    <span class="sm:hidden">History</span>
                </flux:button>
                <flux:button variant="primary" href="#" wire:navigate class="flex-1 sm:flex-none">
                    <flux:icon.plus class="size-4" />
                    <span class="hidden sm:inline">New Submission</span>
                    <span class="sm:hidden">New</span>
                </flux:button>
            </div>
        </div>

        <!-- Alert: Payment Deadline -->
        <flux:card class="p-4 sm:p-6 border-l-4 border-orange-500 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
            <div class="flex items-start gap-3">
                <flux:icon.exclamation-triangle class="size-6 flex-shrink-0 text-orange-600 dark:text-orange-400" />
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Payment Deadline Reminder</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">All salary payments for January 2025 must be completed before February 1, 2025. You have 19 days remaining.</p>
                </div>
            </div>
        </flux:card>

        <!-- Current Month Summary -->
        <div class="grid gap-4 md:grid-cols-4">
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Submissions</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">18</p>
                    </div>
                    <flux:icon.document-text class="size-8 text-blue-600 dark:text-blue-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Amount</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 486,250</p>
                    </div>
                    <flux:icon.wallet class="size-8 text-purple-600 dark:text-purple-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Completed</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">11</p>
                    </div>
                    <flux:icon.check-circle class="size-8 text-green-600 dark:text-green-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">7</p>
                    </div>
                    <flux:icon.clock class="size-8 text-orange-600 dark:text-orange-400" />
                </div>
            </flux:card>
        </div>

        <!-- Quick Submission Form -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Quick Salary Submission</h2>

            <div class="grid gap-6 md:grid-cols-2">
                <!-- Left Column -->
                <div class="space-y-4">
                    <div>
                        <flux:label>Submission Type</flux:label>
                        <div class="mt-2 flex gap-4">
                            <flux:radio.group>
                                <flux:radio label="Single Worker" checked />
                                <flux:radio label="Batch Submission" />
                            </flux:radio.group>
                        </div>
                    </div>

                    <div>
                        <flux:label>Select Client</flux:label>
                        <flux:select placeholder="Choose a client">
                            <flux:select.option>Miqabina Sdn Bhd</flux:select.option>
                            <flux:select.option>WCT Berhad</flux:select.option>
                            <flux:select.option>Chuan Luck Piling Sdn Bhd</flux:select.option>
                            <flux:select.option>Best Stone Sdn Bhd</flux:select.option>
                            <flux:select.option>AIMA Construction Sdn Bhd</flux:select.option>
                        </flux:select>
                    </div>

                    <div>
                        <flux:label>Select Worker</flux:label>
                        <flux:select placeholder="Choose a worker">
                            <flux:select.option>EMP001 - Jefri Aldi Kurniawan</flux:select.option>
                            <flux:select.option>EMP002 - Siti Nurhaliza</flux:select.option>
                            <flux:select.option>EMP003 - Chit Win Maung</flux:select.option>
                            <flux:select.option>EMP004 - Mojahidul Rohim</flux:select.option>
                            <flux:select.option>EMP005 - Ghulam Abbas</flux:select.option>
                        </flux:select>
                    </div>

                    <div>
                        <flux:label>Pay Period</flux:label>
                        <flux:select placeholder="Select period">
                            <flux:select.option>January 2025</flux:select.option>
                            <flux:select.option>December 2024</flux:select.option>
                            <flux:select.option>November 2024</flux:select.option>
                        </flux:select>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4">
                    <div>
                        <flux:label>Basic Salary (RM)</flux:label>
                        <flux:input type="number" placeholder="0.00" value="3500.00" />
                    </div>

                    <div>
                        <flux:label>Hours Worked</flux:label>
                        <flux:input type="number" placeholder="0" value="176" />
                    </div>

                    <div>
                        <flux:label>Overtime Hours</flux:label>
                        <flux:input type="number" placeholder="0" value="8" />
                    </div>

                    <div>
                        <flux:label>Allowances (RM)</flux:label>
                        <flux:input type="number" placeholder="0.00" value="200.00" />
                    </div>

                    <div>
                        <flux:label>Deductions (RM)</flux:label>
                        <flux:input type="number" placeholder="0.00" value="0.00" />
                    </div>
                </div>
            </div>

            <!-- Calculation Summary -->
            <div class="mt-6 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4">
                <h3 class="mb-3 font-semibold text-zinc-900 dark:text-zinc-100">Payment Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">Basic Salary:</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">RM 3,500.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">Overtime (8 hours × RM 25/hr):</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">RM 200.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">Allowances:</span>
                        <span class="font-medium text-zinc-900 dark:text-zinc-100">RM 200.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-600 dark:text-zinc-400">Deductions:</span>
                        <span class="font-medium text-red-600 dark:text-red-400">- RM 0.00</span>
                    </div>
                    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2"></div>
                    <div class="flex justify-between text-base">
                        <span class="font-semibold text-zinc-900 dark:text-zinc-100">Total Payment:</span>
                        <span class="text-xl font-bold text-green-600 dark:text-green-400">RM 3,900.00</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end gap-3">
                <flux:button variant="ghost">Cancel</flux:button>
                <flux:button variant="outline">Save as Draft</flux:button>
                <flux:button variant="primary">
                    <flux:icon.credit-card class="size-4" />
                    Proceed to Payment
                </flux:button>
            </div>
        </flux:card>

        <!-- Recent Submissions -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Recent Submissions</h2>
                <flux:button variant="ghost" size="sm" href="#" wire:navigate>View all</flux:button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Submission ID</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Client</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Workers</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Period</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Amount</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Payment</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY001234</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">Miqabina Sdn Bhd</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">12 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Jan 2025</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 45,200</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Completed</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm" icon="check">Paid</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="document">Download Receipt</flux:menu.item>
                                        <flux:menu.item icon="printer">Print Payslip</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY001233</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">WCT Berhad</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">8 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Jan 2025</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 32,100</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Completed</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm" icon="check">Paid</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="document">Download Receipt</flux:menu.item>
                                        <flux:menu.item icon="printer">Print Payslip</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY001232</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">Chuan Luck Piling Sdn Bhd</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">6 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Jan 2025</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 28,500</td>
                            <td class="py-3">
                                <flux:badge color="yellow" size="sm">Pending</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:badge color="orange" size="sm" icon="clock">Awaiting</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="credit-card">Process Payment</flux:menu.item>
                                        <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Cancel</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY001231</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">Best Stone Sdn Bhd</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">15 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Jan 2025</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 52,800</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Completed</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm" icon="check">Paid</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="document">Download Receipt</flux:menu.item>
                                        <flux:menu.item icon="printer">Print Payslip</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">#PAY001230</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">AIMA Construction Sdn Bhd</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">5 workers</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Jan 2025</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 18,900</td>
                            <td class="py-3">
                                <flux:badge color="yellow" size="sm">Pending</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:badge color="orange" size="sm" icon="clock">Awaiting</flux:badge>
                            </td>
                            <td class="py-3">
                                <flux:dropdown>
                                    <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                    <flux:menu>
                                        <flux:menu.item icon="eye">View Details</flux:menu.item>
                                        <flux:menu.item icon="credit-card">Process Payment</flux:menu.item>
                                        <flux:menu.item icon="pencil">Edit</flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item icon="trash" variant="danger">Cancel</flux:menu.item>
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
                    Showing 1 to 5 of 18 submissions
                </p>
                <div class="flex gap-2">
                    <flux:button variant="ghost" size="sm" disabled>Previous</flux:button>
                    <flux:button variant="outline" size="sm">1</flux:button>
                    <flux:button variant="ghost" size="sm">2</flux:button>
                    <flux:button variant="ghost" size="sm">3</flux:button>
                    <flux:button variant="ghost" size="sm">4</flux:button>
                    <flux:button variant="ghost" size="sm">Next</flux:button>
                </div>
            </div>
        </flux:card>

        <!-- Payment Integration Info -->
        <flux:card class="p-4 sm:p-6 border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex items-start gap-3">
                <flux:icon.information-circle class="size-6 flex-shrink-0 text-blue-600 dark:text-blue-400" />
                <div>
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">Payment via Billplz</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">All payments are securely processed through Billplz. Supported payment methods: FPX Online Banking, Credit/Debit Cards, and e-Wallets.</p>
                </div>
            </div>
        </flux:card>
    </div>
</div>
