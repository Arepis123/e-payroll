<div>
    <!-- Carousel News Notification Modal -->
    <div id="newsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-[2px] opacity-0 invisible transition-all duration-300">
        <div class="relative w-full max-w-2xl mx-4 bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden transform scale-95 transition-transform duration-300">
            <!-- Close Button -->
            <button onclick="closeNewsModal()" class="absolute top-4 right-4 z-10 p-2 rounded-full bg-white/90 dark:bg-zinc-800/90 hover:bg-white dark:hover:bg-zinc-800 transition-colors">
                <flux:icon.x-mark class="size-5 text-zinc-600 dark:text-zinc-400" />
            </button>

            <!-- Carousel Navigation - Top -->
            <div class="absolute top-4 left-1/2 transform -translate-x-1/2 flex items-center gap-4 bg-white/90 dark:bg-zinc-800/90 backdrop-blur-sm px-4 py-2 rounded-full z-10">
                <button onclick="prevSlide()" class="p-1 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-full transition-colors">
                    <flux:icon.chevron-left class="size-4 text-zinc-600 dark:text-zinc-400" />
                </button>

                <div class="flex gap-2">
                    <button onclick="goToSlide(0)" class="carousel-dot w-2 h-2 rounded-full bg-zinc-400 dark:bg-zinc-600 transition-all"></button>
                    <button onclick="goToSlide(1)" class="carousel-dot w-2 h-2 rounded-full bg-zinc-400 dark:bg-zinc-600 transition-all"></button>
                    <button onclick="goToSlide(2)" class="carousel-dot w-2 h-2 rounded-full bg-zinc-400 dark:bg-zinc-600 transition-all"></button>
                </div>

                <button onclick="nextSlide()" class="p-1 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-full transition-colors">
                    <flux:icon.chevron-right class="size-4 text-zinc-600 dark:text-zinc-400" />
                </button>
            </div>

            <!-- Carousel Container -->
            <div id="carouselContainer" class="relative pt-14">
                <!-- Slide 1: Welcome -->
                <div class="carousel-slide active">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/3 bg-gradient-to-br from-blue-500 to-purple-600 p-8 flex items-center justify-center">
                            <flux:icon.hand-raised class="size-24 text-white opacity-90" />
                        </div>
                        <div class="md:w-2/3 p-8">
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">Welcome Back!</h2>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                                Hello, <span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ auth()->user()->name }}</span>!
                                You have successfully logged in to e-Salary CLAB system.
                            </p>
                            <div class="flex items-center gap-2 text-sm text-zinc-600 dark:text-zinc-400">
                                <flux:icon.calendar class="size-4" />
                                <span>{{ now()->format('l, F j, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Outstanding Balance Alert -->
                <div class="carousel-slide">
                    <div class="flex flex-col md:flex-row">
                        <div class="md:w-1/3 bg-gradient-to-br from-orange-500 to-red-600 p-8 flex items-center justify-center">
                            <flux:icon.exclamation-triangle class="size-24 text-white opacity-90" />
                        </div>
                        <div class="md:w-2/3 p-8">
                            <h2 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">Outstanding Balance</h2>
                            <p class="text-zinc-600 dark:text-zinc-400 mb-4">
                                There is an outstanding balance of <span class="font-bold text-orange-600 dark:text-orange-400 text-xl">RM {{ number_format($stats['outstanding_balance']) }}</span> in unpaid invoices that need to be settled.
                            </p>
                            <flux:button variant="primary" href="{{ route('admin.salary') }}" wire:navigate onclick="closeNewsModal()">
                                <flux:icon.wallet class="size-4" />
                                View Salary Management
                            </flux:button>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: Image Slide -->
                <div class="carousel-slide">
                    <div class="flex flex-col">
                        <div class="w-full">
                            <img src="{{ asset('images/test.jpg') }}" alt="Announcement" class="w-full h-auto object-cover max-h-96" />
                        </div>
                        <div class="p-6 bg-white dark:bg-zinc-900">
                            <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100 mb-2">Special Announcement</h2>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">
                                Stay tuned for more updates and features coming to e-Salary CLAB.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-zinc-200 dark:bg-zinc-700">
                <div id="progressBar" class="h-full bg-blue-600 transition-all duration-300" style="width: 33.33%"></div>
            </div>
        </div>
    </div>

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
            <!-- Total Clients -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Clients</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_clients'] }}</p>
                    </div>
                    <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-3">
                        <flux:icon.building-office-2 class="size-6 text-blue-600 dark:text-blue-400" />
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-green-600 dark:text-green-400">+{{ $stats['clients_growth'] }}</span>
                    <span class="text-zinc-600 dark:text-zinc-400">from last month</span>
                </div>
            </flux:card>

            <!-- Active Workers -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
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
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
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

            <!-- Outstanding Balance -->
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
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
                        <flux:button variant="primary" class="w-full" href="#" wire:navigate>
                            <flux:icon.plus class="size-4" />
                            Add New Client
                        </flux:button>
                        <flux:button variant="outline" class="w-full" href="#" wire:navigate>
                            <flux:icon.users class="size-4" />
                            Manage Workers
                        </flux:button>
                        <flux:button variant="outline" class="w-full" href="#" wire:navigate>
                            <flux:icon.document-text class="size-4" />
                            Generate Report
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

        // Carousel News Modal
        let currentSlide = 0;
        let autoSlideInterval;

        function showNewsModal() {
            const modal = document.getElementById('newsModal');
            if (modal) {
                modal.classList.remove('opacity-0', 'invisible');
                modal.querySelector('.transform').classList.remove('scale-95');
                modal.querySelector('.transform').classList.add('scale-100');

                // Start auto-sliding
                startAutoSlide();
            }
        }

        function closeNewsModal() {
            const modal = document.getElementById('newsModal');
            if (modal) {
                modal.classList.add('opacity-0', 'invisible');
                modal.querySelector('.transform').classList.remove('scale-100');
                modal.querySelector('.transform').classList.add('scale-95');

                // Stop auto-sliding
                stopAutoSlide();
            }
        }

        function goToSlide(index) {
            const slides = document.querySelectorAll('.carousel-slide');
            const dots = document.querySelectorAll('.carousel-dot');
            const progressBar = document.getElementById('progressBar');

            // Hide all slides
            slides.forEach(slide => {
                slide.classList.remove('active');
                slide.style.display = 'none';
            });

            // Show current slide
            slides[index].style.display = 'block';
            setTimeout(() => {
                slides[index].classList.add('active');
            }, 10);

            // Update dots
            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.add('bg-blue-600', 'dark:bg-blue-500', 'w-6');
                    dot.classList.remove('bg-zinc-400', 'dark:bg-zinc-600', 'w-2');
                } else {
                    dot.classList.remove('bg-blue-600', 'dark:bg-blue-500', 'w-6');
                    dot.classList.add('bg-zinc-400', 'dark:bg-zinc-600', 'w-2');
                }
            });

            // Update progress bar
            const progress = ((index + 1) / slides.length) * 100;
            progressBar.style.width = progress + '%';

            currentSlide = index;

            // Reset auto-slide timer
            stopAutoSlide();
            startAutoSlide();
        }

        function nextSlide() {
            const slides = document.querySelectorAll('.carousel-slide');
            const nextIndex = (currentSlide + 1) % slides.length;
            goToSlide(nextIndex);
        }

        function prevSlide() {
            const slides = document.querySelectorAll('.carousel-slide');
            const prevIndex = (currentSlide - 1 + slides.length) % slides.length;
            goToSlide(prevIndex);
        }

        function startAutoSlide() {
            autoSlideInterval = setInterval(() => {
                nextSlide();
            }, 5000); // Change slide every 5 seconds
        }

        function stopAutoSlide() {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
            }
        }

        // Show modal on page load (always)
        document.addEventListener('DOMContentLoaded', function() {
            // Always show modal when user visits Dashboard
            setTimeout(() => {
                showNewsModal();
            }, 500); // Show after 500ms delay

            // Initialize first slide
            goToSlide(0);

            // Add keyboard navigation
            document.addEventListener('keydown', function(e) {
                const modal = document.getElementById('newsModal');
                if (!modal.classList.contains('invisible')) {
                    if (e.key === 'ArrowLeft') {
                        prevSlide();
                    } else if (e.key === 'ArrowRight') {
                        nextSlide();
                    } else if (e.key === 'Escape') {
                        closeNewsModal();
                    }
                }
            });

            // Close modal when clicking outside
            document.getElementById('newsModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeNewsModal();
                }
            });
        });
    </script>
</div>
