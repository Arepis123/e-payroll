@php
    // Calculate salary details
    $basicSalary = $worker->basic_salary ?? $worker->wkr_salary ?? 1700; // Minimum RM 1,700
    $epfDeduction = $basicSalary * 0.02; // 2% EPF deduction
    $netSalary = $basicSalary - $epfDeduction;

    // Contract information
    $contractActive = $contract && $contract->isActive();
    $daysRemaining = $contract ? $contract->daysRemaining() : 0;
@endphp

<x-layouts.app :title="__('Worker Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $worker->name }}</h1>
                        @if($contractActive)
                            <flux:badge color="green">Active Contract</flux:badge>
                        @else
                            <flux:badge color="zinc">Inactive</flux:badge>
                        @endif
                    </div>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Worker ID: {{ $worker->wkr_id }} • Passport: {{ $worker->ic_number }}</p>
                </div>
            </div>
            <div>
                <flux:button variant="filled" icon="arrow-left" href="{{ route('admin.worker') }}" wire:navigate>
                    Back to Workers
                </flux:button>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            <!-- Main Information (Left - 2 columns) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Personal Information</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Full Name</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Passport Number</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->ic_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Passport Expiry Date</p>
                            @php
                                $passportExpired = $worker->wkr_passexp && $worker->wkr_passexp->isPast();
                                $passportExpiringSoon = $worker->wkr_passexp && $worker->wkr_passexp->isFuture() && now()->diffInDays($worker->wkr_passexp, false) <= 90;
                            @endphp
                            <p class="text-base font-medium {{ $passportExpired ? 'text-red-600 dark:text-red-400' : ($passportExpiringSoon ? 'text-orange-600 dark:text-orange-400' : 'text-zinc-900 dark:text-zinc-100') }}">
                                @if($worker->wkr_passexp)
                                    {{ $worker->wkr_passexp->format('F d, Y') }}
                                    @if($passportExpired)
                                        <span class="text-xs">(Expired)</span>
                                    @elseif($passportExpiringSoon)
                                        <span class="text-xs">(Expiring Soon)</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Permit Expiry Date</p>
                            @php
                                $permitExpired = $worker->wkr_permitexp && $worker->wkr_permitexp->isPast();
                                $permitExpiringSoon = $worker->wkr_permitexp && $worker->wkr_permitexp->isFuture() && now()->diffInDays($worker->wkr_permitexp, false) <= 60;
                            @endphp
                            <p class="text-base font-medium {{ $permitExpired ? 'text-red-600 dark:text-red-400' : ($permitExpiringSoon ? 'text-orange-600 dark:text-orange-400' : 'text-zinc-900 dark:text-zinc-100') }}">
                                @if($worker->wkr_permitexp)
                                    {{ $worker->wkr_permitexp->format('F d, Y') }}
                                    @if($permitExpired)
                                        <span class="text-xs">(Expired)</span>
                                    @elseif($permitExpiringSoon)
                                        <span class="text-xs">(Expiring Soon)</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Position/Trade</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">
                                @if($worker->workTrade)
                                    {{ $worker->workTrade->trade_desc }}
                                @else
                                    General Worker
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Nationality</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">
                                @if($worker->country)
                                    {{ $worker->country->cty_desc }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Gender</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">
                                @if($worker->wkr_gender == 1)
                                    Male
                                @elseif($worker->wkr_gender == 2)
                                    Female
                                @else
                                    {{ $worker->wkr_gender ?? '-' }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Phone</p>
                            <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->wkr_tel ?? '-' }}</p>
                        </div>
                    </div>
                </flux:card>

                <!-- Contract Information -->
                @if($contract)
                    <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Contract Information</h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Contract Start Date</p>
                                <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $contract->con_start->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Contract End Date</p>
                                <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $contract->con_end->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Contract Period</p>
                                <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">{{ $contract->con_period }} months</p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Days Remaining</p>
                                <p class="text-base font-medium {{ $daysRemaining < 30 ? 'text-orange-600 dark:text-orange-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                                    {{ $daysRemaining }} days
                                    @if($daysRemaining < 30 && $daysRemaining > 0)
                                        <span class="text-xs">(Expiring Soon)</span>
                                    @elseif($daysRemaining <= 0)
                                        <span class="text-xs text-red-600 dark:text-red-400">(Expired)</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Contract Status</p>
                                <div class="mt-1">
                                    @if($contractActive)
                                        <flux:badge color="green">Active</flux:badge>
                                    @else
                                        <flux:badge color="red">Expired</flux:badge>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Current Employer</p>
                                <p class="text-base font-medium text-zinc-900 dark:text-zinc-100">
                                    @if($worker->contractor)
                                        {{ $worker->contractor->ctr_comp_name }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($daysRemaining > 0 && $daysRemaining < 30)
                            <div class="mt-4 p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                                <div class="flex gap-3">
                                    <flux:icon.exclamation-triangle class="size-5 flex-shrink-0 text-orange-600 dark:text-orange-400" />
                                    <div>
                                        <p class="text-sm font-medium text-orange-900 dark:text-orange-100">Contract Expiring Soon</p>
                                        <p class="text-xs text-orange-700 dark:text-orange-300 mt-1">
                                            This worker's contract will expire in {{ $daysRemaining }} days. Please renew the contract or make necessary arrangements.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </flux:card>
                @endif

                <!-- Salary Information -->
                <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Salary Information</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-zinc-200 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-400">Basic Salary</span>
                            <span class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">RM {{ number_format($basicSalary, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-zinc-200 dark:border-zinc-700">
                            <div>
                                <span class="text-zinc-600 dark:text-zinc-400">EPF Deduction (2%)</span>
                                <p class="text-xs text-zinc-500 dark:text-zinc-500">Employee Provident Fund</p>
                            </div>
                            <span class="text-lg font-semibold text-red-600 dark:text-red-400">- RM {{ number_format($epfDeduction, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3 bg-green-50 dark:bg-green-900/20 px-4 rounded-lg">
                            <span class="text-base font-medium text-green-900 dark:text-green-100">Net Salary</span>
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">RM {{ number_format($netSalary, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex gap-3">
                            <flux:icon.information-circle class="size-5 flex-shrink-0 text-blue-600 dark:text-blue-400" />
                            <div>
                                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">Salary Policy for Foreign Construction Workers</p>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                    Minimum salary: RM 1,700 • EPF deduction: 2% of basic salary
                                </p>
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Employment History -->
                @if($contractHistory && $contractHistory->count() > 0)
                    <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Employment History</h2>
                        <div class="space-y-4">
                            @foreach($contractHistory as $index => $pastContract)
                                @php
                                    $isCurrentContract = $contract && $contract->con_id === $pastContract->con_id;
                                    $contractContractor = $pastContract->contractor;
                                @endphp
                                <div class="relative pl-6 pb-4 {{ $index < $contractHistory->count() - 1 ? 'border-l-2 border-zinc-200 dark:border-zinc-700' : '' }}">
                                    <!-- Timeline dot -->
                                    <div class="absolute left-0 top-0 -translate-x-1/2">
                                        @if($isCurrentContract)
                                            <div class="size-4 rounded-full bg-green-500 border-2 border-white dark:border-zinc-900"></div>
                                        @else
                                            <div class="size-4 rounded-full bg-zinc-300 dark:bg-zinc-600 border-2 border-white dark:border-zinc-900"></div>
                                        @endif
                                    </div>

                                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-lg p-4">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-zinc-900 dark:text-zinc-100">
                                                    {{ $contractContractor ? $contractContractor->ctr_comp_name : 'Unknown Contractor' }}
                                                </h3>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                                                    {{ $contractContractor ? $contractContractor->ctr_clab_no : 'N/A' }}
                                                </p>
                                            </div>
                                            @if($isCurrentContract)
                                                <flux:badge color="green">Current</flux:badge>
                                            @elseif($pastContract->isActive())
                                                <flux:badge color="blue">Active</flux:badge>
                                            @else
                                                <flux:badge color="zinc">Expired</flux:badge>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-2 gap-3 mt-3 text-sm">
                                            <div>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Start Date</p>
                                                <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $pastContract->con_start->format('d M Y') }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">End Date</p>
                                                <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $pastContract->con_end->format('d M Y') }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Duration</p>
                                                <p class="font-medium text-zinc-900 dark:text-zinc-100">
                                                    {{ $pastContract->con_period }} months
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-zinc-600 dark:text-zinc-400">Status</p>
                                                <p class="font-medium {{ $pastContract->isActive() ? 'text-green-600 dark:text-green-400' : 'text-zinc-600 dark:text-zinc-400' }}">
                                                    {{ $pastContract->isActive() ? 'Active' : 'Expired' }}
                                                </p>
                                            </div>
                                        </div>

                                        @if(!$pastContract->isActive() && !$isCurrentContract)
                                            <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-700">
                                                <p class="text-xs text-zinc-500 dark:text-zinc-500">
                                                    Contract ended {{ $pastContract->con_end->diffForHumans() }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($contractHistory->count() === 1)
                            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="flex gap-2">
                                    <flux:icon.information-circle class="size-4 flex-shrink-0 text-blue-600 dark:text-blue-400 mt-0.5" />
                                    <p class="text-xs text-blue-700 dark:text-blue-300">
                                        This is the first employment contract for this worker.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </flux:card>
                @endif                
            </div>

            <!-- Sidebar (Right - 1 column) -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Quick Stats</h2>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-2">
                                    <flux:icon.check-circle class="size-5 text-green-600 dark:text-green-400" />
                                </div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Status</span>
                            </div>
                            @if($contractActive)
                                <flux:badge color="green">Active</flux:badge>
                            @else
                                <flux:badge color="zinc">Inactive</flux:badge>
                            @endif
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="rounded-full bg-blue-100 dark:bg-blue-900/30 p-2">
                                    <flux:icon.calendar class="size-5 text-blue-600 dark:text-blue-400" />
                                </div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Contract Days Left</span>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $daysRemaining }} days</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="rounded-full bg-purple-100 dark:bg-purple-900/30 p-2">
                                    <flux:icon.wallet class="size-5 text-purple-600 dark:text-purple-400" />
                                </div>
                                <span class="text-sm text-zinc-600 dark:text-zinc-400">Net Salary</span>
                            </div>
                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">RM {{ number_format($netSalary, 2) }}</span>
                        </div>
                    </div>
                </flux:card>

                <!-- Actions -->
                <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Actions</h2>
                    <div class="space-y-2">
                        <flux:button variant="primary" class="w-full">
                            <flux:icon.document-text class="size-4" />
                            View Payslips
                        </flux:button>
                        <flux:button variant="outline" class="w-full">
                            <flux:icon.calendar class="size-4" />
                            View Attendance
                        </flux:button>
                        <flux:button variant="outline" class="w-full">
                            <flux:icon.document-duplicate class="size-4" />
                            Generate Report
                        </flux:button>
                    </div>
                </flux:card>

                <!-- Next of Kin Information -->
                <flux:card class="p-6 dark:bg-zinc-900 rounded-lg">
                    <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Next of Kin</h2>
                    <div class="space-y-3">
                        @if($worker->wkr_next_of_kin || $worker->wkr_relationship || $worker->wkr_homeaddr)
                            @if($worker->wkr_next_of_kin)
                                <div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">Name</p>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->wkr_next_of_kin }}</p>
                                </div>
                            @endif

                            @if($worker->wkr_relationship)
                                <div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">Relationship</p>
                                    <p class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->wkr_relationship }}</p>
                                </div>
                            @endif

                            @if($worker->wkr_homeaddr)
                                <div>
                                    <p class="text-xs text-zinc-600 dark:text-zinc-400">Home Address</p>
                                    <p class="text-sm text-zinc-900 dark:text-zinc-100">{{ $worker->wkr_homeaddr }}</p>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">No next of kin information available</p>
                            </div>
                        @endif
                    </div>
                </flux:card>
            </div>
        </div>
    </div>
</x-layouts.app>
