<x-layouts.app :title="__('Timesheet Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Edit Draft Submission</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Editing draft for {{ $submission->month_year }}</p>
            </div>
            <flux:button variant="filled" icon="arrow-left" href="{{ route('client.timesheet') }}">
                Back to Timesheet
            </flux:button>
        </div>

        @if(session('success'))
            <div class="rounded-lg bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800">
                <p class="text-sm text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error') || isset($error))
            <div class="rounded-lg bg-red-50 dark:bg-red-900/20 p-4 border border-red-200 dark:border-red-800">
                <p class="text-sm text-red-800 dark:text-red-200">{{ session('error') ?? $error }}</p>
            </div>
        @endif

        @if(!isset($error))
        <!-- Current Month Info -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $period['month_name'] }} {{ $period['year'] }} Payroll</h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                        Payment deadline: <span class="font-semibold {{ $period['days_until_deadline'] < 7 ? 'text-red-600 dark:text-red-400' : 'text-orange-600 dark:text-orange-400' }}">
                            {{ $period['deadline']->format('F d, Y') }} ({{ $period['days_until_deadline'] }} days remaining)
                        </span>
                    </p>
                </div>
                @if($currentSubmission->status === 'draft')
                    <flux:badge color="zinc" size="lg">Draft</flux:badge>
                @elseif($currentSubmission->status === 'pending_payment')
                    <flux:badge color="orange" size="lg">Pending Payment</flux:badge>
                @elseif($currentSubmission->status === 'paid')
                    <flux:badge color="green" size="lg">Paid</flux:badge>
                @elseif($currentSubmission->status === 'overdue')
                    <flux:badge color="red" size="lg">Overdue</flux:badge>
                @endif
            </div>
        </flux:card>

        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-4">
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Submissions</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_submissions'] }}</p>
                    </div>
                    <flux:icon.document-text class="size-8 text-blue-600 dark:text-blue-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Paid</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['paid_submissions'] }}</p>
                    </div>
                    <flux:icon.check-circle class="size-8 text-green-600 dark:text-green-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Pending</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['pending_submissions'] }}</p>
                    </div>
                    <flux:icon.clock class="size-8 text-orange-600 dark:text-orange-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Workers</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $workers->count() }}</p>
                    </div>
                    <flux:icon.users class="size-8 text-purple-600 dark:text-purple-400" />
                </div>
            </flux:card>
        </div>

        <!-- Payroll Entry Form -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $period['month_name'] }} {{ $period['year'] }} - Worker Hours & Overtime</h2>

            <!-- Calculation Information -->
            <div class="mb-4 grid gap-3 lg:grid-cols-2">
                <!-- Salary Breakdown Info -->
                <div class="rounded-lg bg-green-50 dark:bg-green-900/20 p-4 border border-green-200 dark:border-green-800">
                    <div class="flex gap-3">
                        <flux:icon.currency-dollar class="size-5 flex-shrink-0 text-green-600 dark:text-green-400" />
                        <div class="text-sm text-green-900 dark:text-green-100">
                            <p class="font-medium">Salary Calculation (Based on RM 1,700 minimum):</p>
                            <div class="mt-2 grid grid-cols-2 gap-x-6 gap-y-1 text-xs text-green-700 dark:text-green-300">
                                <div><strong>Worker Receives:</strong></div>
                                <div>RM 1,657.50 (Basic - EPF 2% - SOCSO 0.5%)</div>
                                <div><strong>System Collects:</strong></div>
                                <div>RM 1,763.75 (Basic + Employer EPF 2% + Employer SOCSO 1.75%)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overtime Rates Info -->
                <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 p-4 border border-blue-200 dark:border-blue-800">
                    <div class="flex gap-3">
                        <flux:icon.clock class="size-5 flex-shrink-0 text-blue-600 dark:text-blue-400" />
                        <div class="text-sm text-blue-900 dark:text-blue-100">
                            <p class="font-medium">Overtime Rates (Hourly Rate: RM 8.17):</p>
                            <div class="mt-2 grid grid-cols-3 gap-x-4 gap-y-1 text-xs text-blue-700 dark:text-blue-300">
                                <div><strong>Normal Day:</strong> RM 12.26/hr (1.5x)</div>
                                <div><strong>Rest Day:</strong> RM 16.34/hr (2.0x)</div>
                                <div><strong>Public Holiday:</strong> RM 24.51/hr (3.0x)</div>
                            </div>
                            <p class="mt-2 text-xs text-blue-600 dark:text-blue-400 italic">Note: This month's OT is calculated now but paid NEXT month. EPF/SOCSO applies to total (Basic + Previous Month OT)</p>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('client.timesheet.store') }}" id="timesheetForm">
                @csrf
                <input type="hidden" name="draft_id" value="{{ $submission->id }}">

                <!-- Selection Controls -->
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">
                            <span id="selectedCount">{{ count($workersData) }}</span> of {{ count($workersData) }} workers selected
                        </span>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-700">
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400 w-12">Select</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Worker Name</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Employee ID</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Basic Salary</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">OT Normal (hrs)</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">OT Rest Day (hrs)</th>
                                <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">OT Public (hrs)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @foreach($workersData as $index => $worker)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 worker-row" data-worker-index="{{ $index }}">
                                <td class="py-3">
                                    <input
                                        type="checkbox"
                                        name="workers[{{ $index }}][included]"
                                        value="1"
                                        class="worker-checkbox size-4 rounded border-zinc-300 dark:border-zinc-700"
                                        {{ $worker->included ? 'checked' : '' }}
                                    />
                                </td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <flux:avatar size="sm" name="{{ $worker->worker_name }}" />
                                        <div>
                                            <div class="text-sm text-zinc-900 dark:text-zinc-100">{{ $worker->worker_name }}</div>
                                            <div class="text-xs text-zinc-500 dark:text-zinc-400">{{ $worker->worker_passport }}</div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="workers[{{ $index }}][worker_id]" value="{{ $worker->worker_id }}">
                                    <input type="hidden" name="workers[{{ $index }}][worker_name]" value="{{ $worker->worker_name }}">
                                    <input type="hidden" name="workers[{{ $index }}][worker_passport]" value="{{ $worker->worker_passport }}">
                                </td>
                                <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->worker_id }}</td>
                                <td class="py-3">
                                    <flux:input
                                        type="number"
                                        name="workers[{{ $index }}][basic_salary]"
                                        class="w-32"
                                        value="{{ $worker->basic_salary }}"
                                        min="1700"
                                        step="0.01"
                                        readonly
                                    />
                                </td>
                                <td class="py-3 px-2">
                                    <flux:input
                                        type="number"
                                        name="workers[{{ $index }}][ot_normal_hours]"
                                        class="w-24"
                                        value="{{ $worker->ot_normal_hours ?? 0 }}"
                                        min="0"
                                        step="0.5"
                                    />
                                </td>
                                <td class="py-3 px-2">
                                    <flux:input
                                        type="number"
                                        name="workers[{{ $index }}][ot_rest_hours]"
                                        class="w-24"
                                        value="{{ $worker->ot_rest_hours ?? 0 }}"
                                        min="0"
                                        step="0.5"
                                    />
                                </td>
                                <td class="py-3 px-2">
                                    <flux:input
                                        type="number"
                                        name="workers[{{ $index }}][ot_public_hours]"
                                        class="w-24"
                                        value="{{ $worker->ot_public_hours ?? 0 }}"
                                        min="0"
                                        step="0.5"
                                    />
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-between items-center">
                    <flux:button variant="filled" icon="arrow-left" href="{{ route('client.timesheet') }}">
                        Cancel
                    </flux:button>
                    <div class="flex gap-2">
                        <flux:button type="submit" variant="filled" name="action" value="draft">
                            <!-- <flux:icon.document class="size-4" /> -->
                            Update Draft
                        </flux:button>
                        <flux:button type="submit" variant="primary" name="action" value="submit">
                            <!-- <flux:icon.check class="size-4" /> -->
                            Submit for Payment
                        </flux:button>
                    </div>
                </div>
            </form>
        </flux:card>

        <!-- Submission History -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <h2 class="mb-4 text-lg font-semibold text-zinc-900 dark:text-zinc-100">Recent Submissions</h2>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Period</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Submitted Date</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Workers</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Total Amount</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Penalty</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($recentSubmissions as $submission)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $submission->month_year }}</td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : 'Not submitted' }}
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $submission->total_workers }}</td>
                            <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                RM {{ number_format($submission->total_amount, 2) }}
                            </td>
                            <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">
                                @if($submission->has_penalty)
                                    <span class="text-red-600 dark:text-red-400">+ RM {{ number_format($submission->penalty_amount, 2) }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3">
                                @if($submission->status === 'draft')
                                    <flux:badge color="zinc" size="sm">Draft</flux:badge>
                                @elseif($submission->status === 'pending_payment')
                                    <flux:badge color="orange" size="sm">Pending Payment</flux:badge>
                                @elseif($submission->status === 'paid')
                                    <flux:badge color="green" size="sm">Paid</flux:badge>
                                @elseif($submission->status === 'overdue')
                                    <flux:badge color="red" size="sm">Overdue</flux:badge>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="flex gap-2">
                                    @if($submission->status === 'draft')
                                        <flux:button variant="primary" size="sm" icon="pencil" href="{{ route('client.timesheet.edit', $submission->id) }}">
                                            Edit Draft
                                        </flux:button>
                                    @endif
                                    @if($submission->status === 'pending_payment' || $submission->status === 'overdue')
                                        <form method="POST" action="{{ route('client.payment.create', $submission->id) }}" class="inline">
                                            @csrf
                                            <flux:button type="submit" variant="primary" size="sm">
                                                <flux:icon.credit-card class="size-4 inline" />
                                                Pay Now
                                            </flux:button>
                                        </form>
                                    @endif
                                    <flux:button variant="filled" size="sm" icon="eye" href="{{ route('client.timesheet.show', $submission->id) }}">
                                        View
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-zinc-600 dark:text-zinc-400">
                                No submissions yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </flux:card>
        @endif
    </div>

    <script>
        // Simple form validation before submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('timesheetForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Find all worker checkboxes by name pattern
                    const checkboxInputs = document.querySelectorAll('input[name*="[included]"]');
                    const selectedCount = Array.from(checkboxInputs).filter(cb => cb.checked).length;

                    if (selectedCount === 0) {
                        e.preventDefault();
                        alert('Please select at least one worker to submit payroll.');
                        return false;
                    }
                });
            }

            // Update count when checkboxes change
            setTimeout(function() {
                const checkboxInputs = document.querySelectorAll('input[name*="[included]"]');
                const countElement = document.getElementById('selectedCount');

                function updateCount() {
                    if (countElement) {
                        const selected = Array.from(checkboxInputs).filter(cb => cb.checked).length;
                        countElement.textContent = selected;
                    }
                }

                // Add listeners to each checkbox
                checkboxInputs.forEach(cb => {
                    cb.addEventListener('change', updateCount);
                });

                // Initial count
                updateCount();
            }, 300);
        });
    </script>
</x-layouts.app>
