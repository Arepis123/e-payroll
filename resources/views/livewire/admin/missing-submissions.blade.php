<div class="flex h-full w-full flex-1 flex-col gap-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">Missing Submissions</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Contractors who haven't submitted payroll for {{ now()->format('F Y') }}</p>
        </div>
    </div>

    <!-- Statistics Card -->
    <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
        <div class="flex items-center gap-4">
            <div class="rounded-full bg-orange-100 dark:bg-orange-900/30 p-3">
                <flux:icon.exclamation-triangle class="size-8 text-orange-600 dark:text-orange-400" />
            </div>
            <div>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Contractors Without Submission</p>
                <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $missingContractors->count() }}</p>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 mt-1">
                    {{ Str::plural('contractor', $missingContractors->count()) }} with active workers need to submit
                </p>
            </div>
        </div>
    </flux:card>

    <!-- Contractors Without Submission Table -->
    @if($missingContractors->count() > 0)
    <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Contractors List</h2>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                    Current period: {{ now()->format('F Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <flux:button variant="ghost" size="sm" icon="arrow-path" wire:click="$refresh">
                    Refresh
                </flux:button>
                <flux:button variant="ghost" size="sm" icon="arrow-down-tray">
                    Export
                </flux:button>
            </div>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column align="center"><span class="text-center text-xs font-medium text-zinc-600 dark:text-zinc-400">No</span></flux:table.column>
                <flux:table.column><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">CLAB No</span></flux:table.column>
                <flux:table.column><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Contractor Name</span></flux:table.column>
                <flux:table.column><span class="text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Contact</span></flux:table.column>
                <flux:table.column align="center"><span class="text-center text-xs font-medium text-zinc-600 dark:text-zinc-400">Active Workers</span></flux:table.column>
                <flux:table.column align="center"><span class="text-center text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</span></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($missingContractors as $index => $contractor)
                    <flux:table.rows :key="$contractor['clab_no']">
                        <flux:table.cell align="center">{{ $index + 1 }}</flux:table.cell>

                        <flux:table.cell variant="strong">
                            {{ $contractor['clab_no'] }}
                        </flux:table.cell>

                        <flux:table.cell variant="strong">
                            {{ $contractor['name'] }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="text-sm">
                                @if($contractor['email'])
                                    <div class="flex items-center gap-1 text-zinc-600 dark:text-zinc-400">
                                        <flux:icon.envelope class="size-3" />
                                        <span>{{ $contractor['email'] }}</span>
                                    </div>
                                @endif
                                @if($contractor['phone'])
                                    <div class="flex items-center gap-1 text-zinc-600 dark:text-zinc-400 mt-1">
                                        <flux:icon.phone class="size-3" />
                                        <span>{{ $contractor['phone'] }}</span>
                                    </div>
                                @endif
                                @if(!$contractor['email'] && !$contractor['phone'])
                                    <span class="text-zinc-400 dark:text-zinc-500">No contact info</span>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <flux:badge color="blue" size="sm" inset="top bottom">
                                {{ $contractor['active_workers'] }} {{ Str::plural('worker', $contractor['active_workers']) }}
                            </flux:badge>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <div class="flex items-center justify-center gap-2">
                                <flux:button variant="ghost" size="sm" icon="bell">
                                    Remind
                                </flux:button>
                                <flux:button variant="ghost" size="sm" icon="eye">
                                    View Details
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.rows>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
    @else
    <flux:card class="p-12 text-center dark:bg-zinc-900 rounded-lg">
        <div class="flex flex-col items-center gap-4">
            <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-4">
                <flux:icon.check-circle class="size-12 text-green-600 dark:text-green-400" />
            </div>
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">All Contractors Have Submitted!</h3>
                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">
                    Every contractor with active workers has submitted their payroll for {{ now()->format('F Y') }}.
                </p>
            </div>
        </div>
    </flux:card>
    @endif
</div>
