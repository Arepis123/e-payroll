<div>
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Dashboard</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Overview of your e-payroll system</p>
            </div>
            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ now()->format('l, F j, Y') }}
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Clients Without Submission -->
            <a href="{{ route('missing-submissions') }}" wire:navigate>
                <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg cursor-pointer transition-all duration-300 ease-in-out hover:scale-103 hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending Submissions</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['clients_without_submission'] }}</p>
                        </div>
                        <div class="rounded-full bg-orange-100 dark:bg-orange-900/30 p-3">
                            <flux:icon.exclamation-triangle class="size-6 text-orange-600 dark:text-orange-400" />
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-zinc-600 dark:text-zinc-400">{{ $stats['clients_with_submission_count'] }} of {{ $stats['total_clients'] }} submitted</span>
                    </div>
                </flux:card>
            </a>

            <!-- Active Workers -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg cursor-pointer transition-all duration-300 ease-in-out hover:scale-103 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Active Workers</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['active_workers'] }}</p>
                    </div>
                    <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3">
                        <flux:icon.users class="size-6 text-green-600 dark:text-green-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-green-600 dark:text-green-400">+{{ $stats['workers_growth'] }}</span>
                    <span class="text-zinc-600 dark:text-zinc-400">from last month</span>
                </div>
            </flux:card>

            <!-- This Month Payments -->
            <a href="{{ route('payroll') }}" wire:navigate>
                <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg cursor-pointer transition-all duration-300 ease-in-out hover:scale-103 hover:shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">This Month Payments</p>
                            <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM {{ number_format($stats['this_month_payments']) }}</p>
                        </div>
                        <div class="rounded-full bg-purple-100 dark:bg-purple-900/30 p-3">
                            <flux:icon.wallet class="size-6 text-purple-600 dark:text-purple-400" />
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-green-600 dark:text-green-400">+{{ $stats['payments_growth'] }}%</span>
                        <span class="text-zinc-600 dark:text-zinc-400">from last month</span>
                    </div>
                </flux:card>
            </a>

            <!-- Outstanding Balance -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg cursor-pointer transition-all duration-300 ease-in-out hover:scale-103 hover:shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Outstanding Balance</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">RM {{ number_format($stats['outstanding_balance']) }}</p>
                    </div>
                    <div class="rounded-full bg-orange-100 dark:bg-orange-900/30 p-3">
                        <flux:icon.exclamation-circle class="size-6 text-orange-600 dark:text-orange-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-orange-600 dark:text-orange-400">Unpaid invoices</span>
                </div>
            </flux:card>
        </div>

        <!-- Recent Activity & Quick Actions -->
        <div class="grid gap-4 lg:grid-cols-3">
            <!-- Recent Payments -->
            <flux:card class="lg:col-span-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Recent Payments</h2>
                    <flux:button variant="ghost" size="sm" href="#" wire:navigate>View all</flux:button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Client</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Amount</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Workers</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Date</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($recentPayments as $payment)
                            <tr>
                                <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $payment['client'] }}</td>
                                <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">RM {{ number_format($payment['amount']) }}</td>
                                <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $payment['workers'] }} workers</td>
                                <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $payment['date'] }}</td>
                                <td class="py-3">
                                    <flux:badge color="{{ $payment['status'] === 'completed' ? 'green' : 'yellow' }}" size="sm">
                                        {{ ucfirst($payment['status']) }}
                                    </flux:badge>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </flux:card>

            <!-- Quick Actions & Alerts -->
            <div class="space-y-4">
                <!-- Quick Actions -->
                <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Quick Actions</h2>
                    <div class="space-y-2">
                        <flux:button variant="primary" class="w-full" href="{{ route('payroll') }}" wire:navigate>
                            <flux:icon.wallet class="size-4" />
                            View Payroll
                        </flux:button>
                        <flux:button variant="outline" class="w-full" href="{{ route('workers') }}" wire:navigate>
                            <flux:icon.users class="size-4" />
                            Manage Workers
                        </flux:button>
                        <flux:button variant="outline" class="w-full" href="{{ route('notifications') }}" wire:navigate>
                            <flux:icon.bell class="size-4" />
                            View Reminder
                        </flux:button>
                    </div>
                </flux:card>

                <!-- Alerts -->
                <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Alerts</h2>
                    <div class="space-y-3">
                        <div class="flex gap-3 rounded-lg bg-orange-50 dark:bg-orange-900/20 p-3">
                            <flux:icon.exclamation-triangle class="size-5 flex-shrink-0 text-orange-600 dark:text-orange-400" />
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">Outstanding Balance</p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">RM {{ number_format($stats['outstanding_balance']) }} in unpaid invoices</p>
                            </div>
                        </div>

                        <div class="flex gap-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 p-3">
                            <flux:icon.information-circle class="size-5 flex-shrink-0 text-blue-600 dark:text-blue-400" />
                            <div>
                                <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">New Workers</p>
                                <p class="text-xs text-zinc-600 dark:text-zinc-400">5 new workers added this week</p>
                            </div>
                        </div>
                    </div>
                </flux:card>
            </div>
        </div>

        <!-- Monthly Overview Chart -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Payment Overview</h2>
            <div class="relative h-64 sm:h-80">
                <canvas id="paymentOverviewChart"></canvas>
            </div>
        </flux:card>
    </div>

    <script>
        // Wait for both DOM and Chart.js to be ready
        function initDashboardChart() {
            if (typeof Chart === 'undefined') {
                setTimeout(initDashboardChart, 50);
                return;
            }

            const ctx = document.getElementById('paymentOverviewChart');
            if (!ctx) return;

            // Get theme colors
            const isDark = document.documentElement.classList.contains('dark');
            const textColor = isDark ? '#d4d4d8' : '#3f3f46';
            const gridColor = isDark ? '#3f3f46' : '#e4e4e7';

            const chartData = @json($chartData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Total Payments (RM)',
                        data: chartData.totalPayments,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'Number of Payments',
                        data: chartData.numberOfPayments,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: textColor,
                                usePointStyle: true,
                                padding: 15
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#18181b' : '#ffffff',
                            titleColor: textColor,
                            bodyColor: textColor,
                            borderColor: gridColor,
                            borderWidth: 1,
                            padding: 12,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        if (context.datasetIndex === 0) {
                                            label += 'RM ' + context.parsed.y.toLocaleString();
                                        } else {
                                            label += context.parsed.y;
                                        }
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: gridColor,
                                display: false
                            },
                            ticks: {
                                color: textColor
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: {
                                color: gridColor
                            },
                            ticks: {
                                color: textColor,
                                callback: function(value) {
                                    return 'RM ' + (value / 1000) + 'k';
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                color: textColor
                            }
                        }
                    }
                }
            });
        }

        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initDashboardChart);
        } else {
            initDashboardChart();
        }
    </script>
</div>
