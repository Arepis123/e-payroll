<div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Reports & Analytics</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Generate and view payroll reports</p>
            </div>
            <div class="flex gap-2">
                <flux:button variant="outline" href="#" wire:navigate class="flex-1 sm:flex-none">
                    <flux:icon.arrow-down-tray class="size-4" />
                    <span class="hidden sm:inline">Export All</span>
                    <span class="sm:hidden">Export</span>
                </flux:button>
                <flux:button variant="primary" href="#" wire:navigate class="flex-1 sm:flex-none">
                    <flux:icon.document-plus class="size-4" />
                    <span class="hidden sm:inline">Generate Report</span>
                    <span class="sm:hidden">Generate</span>
                </flux:button>
            </div>
        </div>

        <!-- Report Filters -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Filter Reports</h2>
            <div class="grid gap-4 md:grid-cols-4">
                <div>
                    <flux:label>Report Type</flux:label>
                    <flux:select placeholder="Select type">
                        <flux:select.option>All Reports</flux:select.option>
                        <flux:select.option>Payment Summary</flux:select.option>
                        <flux:select.option>Worker Payroll</flux:select.option>
                        <flux:select.option>Client Summary</flux:select.option>
                        <flux:select.option>Monthly Analysis</flux:select.option>
                        <flux:select.option>Tax Report</flux:select.option>
                    </flux:select>
                </div>

                <div>
                    <flux:label>Period</flux:label>
                    <flux:select placeholder="Select period">
                        <flux:select.option>January 2025</flux:select.option>
                        <flux:select.option>December 2024</flux:select.option>
                        <flux:select.option>November 2024</flux:select.option>
                        <flux:select.option>October 2024</flux:select.option>
                        <flux:select.option>Custom Range</flux:select.option>
                    </flux:select>
                </div>

                <div>
                    <flux:label>Client</flux:label>
                    <flux:select placeholder="Select client">
                        <flux:select.option>All Clients</flux:select.option>
                        <flux:select.option>Miqabina Sdn Bhd</flux:select.option>
                        <flux:select.option>WCT Berhad</flux:select.option>
                        <flux:select.option>Chuan Luck Piling Sdn Bhd</flux:select.option>
                        <flux:select.option>Best Stone Sdn Bhd</flux:select.option>
                    </flux:select>
                </div>

                <div class="flex items-end">
                    <flux:button variant="primary" class="w-full">
                        <flux:icon.funnel class="size-4" />
                        Apply Filters
                    </flux:button>
                </div>
            </div>
        </flux:card>

        <!-- Report Statistics -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Paid (Jan 2025)</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 486,250</p>
                    </div>
                    <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3">
                        <flux:icon.check-circle class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">11 completed payments</p>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending Amount</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 124,800</p>
                    </div>
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900/30 p-3">
                        <flux:icon.clock class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">7 pending payments</p>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Average Salary</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM 2,650</p>
                    </div>
                    <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-3">
                        <flux:icon.calculator class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">Per worker this month</p>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Hours</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">60,192</p>
                    </div>
                    <div class="rounded-full bg-purple-100 dark:bg-purple-900/30 p-3">
                        <flux:icon.clock class="size-6 text-purple-600 dark:text-purple-400" />
                    </div>
                </div>
                <p class="text-xs text-zinc-600 dark:text-zinc-400">Including 2,400 OT hours</p>
            </flux:card>
        </div>

        <!-- Charts Section -->
        <div class="grid gap-4 lg:grid-cols-2">
            <!-- Monthly Payment Trend -->
            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Monthly Payment Trend</h2>
                <div class="relative h-64">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </flux:card>

            <!-- Payment Distribution by Client -->
            <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Payment Distribution by Client</h2>
                <div class="relative h-64">
                    <canvas id="clientDistributionChart"></canvas>
                </div>
            </flux:card>
        </div>

        <!-- Payment Summary by Client -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Payment Summary by Client (January 2025)</h2>
                <flux:button variant="ghost" size="sm">
                    <flux:icon.arrow-down-tray class="size-4" />
                    Export CSV
                </flux:button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Client Name</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Workers</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Hours</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Basic Salary</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Overtime</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Allowances</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Deductions</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Amount</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">Miqabina Sdn Bhd</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">12</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">2,112</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 38,400</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 4,200</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 2,400</td>
                            <td class="py-3 text-sm text-red-600 dark:text-red-400">RM 200</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 45,200</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">WCT Berhad</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">8</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">1,408</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 27,200</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 3,100</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 1,600</td>
                            <td class="py-3 text-sm text-red-600 dark:text-red-400">RM 0</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 32,100</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">Chuan Luck Piling Sdn Bhd</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">6</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">1,056</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 24,000</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 2,800</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 1,200</td>
                            <td class="py-3 text-sm text-red-600 dark:text-red-400">RM 500</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 28,500</td>
                            <td class="py-3">
                                <flux:badge color="orange" size="sm">Pending</flux:badge>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">Best Stone Sdn Bhd</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">15</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">2,640</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 44,000</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 5,600</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 3,000</td>
                            <td class="py-3 text-sm text-red-600 dark:text-red-400">RM 0</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 52,800</td>
                            <td class="py-3">
                                <flux:badge color="green" size="sm">Paid</flux:badge>
                            </td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">AIMA Construction Sdn Bhd</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">5</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">880</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 16,000</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 1,800</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 1,000</td>
                            <td class="py-3 text-sm text-red-600 dark:text-red-400">RM 100</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM 18,900</td>
                            <td class="py-3">
                                <flux:badge color="orange" size="sm">Pending</flux:badge>
                            </td>
                        </tr>

                        <tr class="bg-zinc-100 dark:bg-zinc-800 font-semibold">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">TOTAL</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">46</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">8,096</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 149,600</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 17,500</td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">RM 9,200</td>
                            <td class="py-3 text-sm text-red-600 dark:text-red-400">RM 800</td>
                            <td class="py-3 text-sm font-bold text-zinc-900 dark:text-zinc-100">RM 177,500</td>
                            <td class="py-3"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </flux:card>

        <!-- Worker Payroll Summary -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Top Paid Workers (January 2025)</h2>
                <flux:button variant="ghost" size="sm" href="#" wire:navigate>View all workers</flux:button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Rank</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Employee ID</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Position</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Client</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Hours Worked</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Earned</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">1</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">EMP004</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Mojahidul Rohim" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Mojahidul Rohim</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Best Stone</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">192 hrs</td>
                            <td class="py-3 text-sm font-medium text-green-600 dark:text-green-400">RM 5,100</td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">2</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">EMP001</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Jefri Aldi Kurniawan" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Jefri Aldi Kurniawan</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Miqabina</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">184 hrs</td>
                            <td class="py-3 text-sm font-medium text-green-600 dark:text-green-400">RM 3,900</td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">3</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">EMP003</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Chit Win Maung" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Chit Win Maung</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Chuan Luck Piling</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">176 hrs</td>
                            <td class="py-3 text-sm font-medium text-green-600 dark:text-green-400">RM 3,200</td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">4</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">EMP006</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Heri Siswanto" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Heri Siswanto</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">Carpenter</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Miqabina</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">180 hrs</td>
                            <td class="py-3 text-sm font-medium text-green-600 dark:text-green-400">RM 2,850</td>
                        </tr>

                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">5</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">EMP005</td>
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <flux:avatar size="sm" name="Ghulam Abbas" />
                                    <span class="text-sm text-zinc-900 dark:text-zinc-100">Ghulam Abbas</span>
                                </div>
                            </td>
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">General Worker</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">Miqabina</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">176 hrs</td>
                            <td class="py-3 text-sm font-medium text-green-600 dark:text-green-400">RM 2,400</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </flux:card>

        <!-- Quick Report Templates -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Quick Report Templates</h2>
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 hover:border-blue-500 dark:hover:border-blue-500 cursor-pointer transition">
                    <flux:icon.document-text class="size-8 text-blue-600 dark:text-blue-400 mb-3" />
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-1">Monthly Payroll Summary</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3">Complete payroll summary for the selected month</p>
                    <flux:button variant="outline" size="sm" class="w-full">Generate PDF</flux:button>
                </div>

                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 hover:border-blue-500 dark:hover:border-blue-500 cursor-pointer transition">
                    <flux:icon.users class="size-8 text-green-600 dark:text-green-400 mb-3" />
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-1">Worker Payslips</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3">Individual payslips for all workers</p>
                    <flux:button variant="outline" size="sm" class="w-full">Generate ZIP</flux:button>
                </div>

                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-4 hover:border-blue-500 dark:hover:border-blue-500 cursor-pointer transition">
                    <flux:icon.building-office class="size-8 text-purple-600 dark:text-purple-400 mb-3" />
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 mb-1">Client Billing Report</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-3">Detailed billing report per client</p>
                    <flux:button variant="outline" size="sm" class="w-full">Generate Excel</flux:button>
                </div>
            </div>
        </flux:card>
    </div>

    <script>
        // Wait for both DOM and Chart.js to be ready
        function initReportCharts() {
            if (typeof Chart === 'undefined') {
                setTimeout(initReportCharts, 50);
                return;
            }

            const trendCtx = document.getElementById('monthlyTrendChart');
            const pieCtx = document.getElementById('clientDistributionChart');

            if (!trendCtx || !pieCtx) return;

            // Get theme colors
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#d4d4d8' : '#3f3f46';
            const gridColor = isDark ? '#3f3f46' : '#e4e4e7';

            // Monthly Trend Chart
            new Chart(trendCtx, {
                type: 'bar',
                data: {
                    labels: ['Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan'],
                    datasets: [{
                        label: 'Total Payments (RM)',
                        data: [502000, 475000, 490000, 468000, 485000, 486250],
                        backgroundColor: 'rgba(139, 92, 246, 0.8)',
                        borderColor: '#8b5cf6',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#18181b' : '#ffffff',
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: gridColor,
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'RM ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: textColor
                            }
                        },
                        y: {
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return 'RM ' + (value / 1000) + 'k';
                                }
                            }
                        }
                    }
                }
            });

            // Client Distribution Pie Chart
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Miqabina', 'Miqabina', 'Chuan Luck Piling', 'Best Stone', 'AIMA Construction'],
                    datasets: [{
                        label: 'Payment Amount',
                        data: [45200, 32100, 28500, 52800, 18900],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)',   // Blue
                            'rgba(16, 185, 129, 0.8)',   // Green
                            'rgba(245, 158, 11, 0.8)',   // Orange
                            'rgba(139, 92, 246, 0.8)',   // Purple
                            'rgba(236, 72, 153, 0.8)'    // Pink
                        ],
                        borderColor: [
                            '#3b82f6',
                            '#10b981',
                            '#f59e0b',
                            '#8b5cf6',
                            '#ec4899'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                color: textColor,
                                padding: 15,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#18181b' : '#ffffff',
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: gridColor,
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return label + ': RM ' + value.toLocaleString() + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initReportCharts);
        } else {
            initReportCharts();
        }
    </script>
</div>
