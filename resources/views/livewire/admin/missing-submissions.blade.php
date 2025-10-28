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
                <flux:table.column align="center"><span class="text-center text-xs font-medium text-zinc-600 dark:text-zinc-400">Reminders Sent</span></flux:table.column>
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
                                        <flux:icon.envelope class="size-3 me-1" />
                                        <span>{{ $contractor['email'] }}</span>
                                    </div>
                                @endif
                                @if($contractor['phone'])
                                    <div class="flex items-center gap-1 text-zinc-600 dark:text-zinc-400 mt-1">
                                        <flux:icon.phone class="size-3 me-1" />
                                        <span>{{ $contractor['phone'] }}</span>
                                    </div>
                                @endif
                                @if(!$contractor['email'] && !$contractor['phone'])
                                    <span class="text-zinc-400 dark:text-zinc-500">No contact info</span>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <div class="flex flex-col items-center gap-1">
                                <flux:badge color="orange" size="sm" inset="top bottom">
                                    {{ $contractor['active_workers'] }} of {{ $contractor['total_workers'] }} not submitted
                                </flux:badge>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    {{ $contractor['total_workers'] - $contractor['active_workers'] }} submitted
                                </span>
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <div class="flex flex-col items-center gap-1">
                                @if($contractor['reminders_sent'] > 0)
                                    <flux:badge color="blue" size="sm" inset="top bottom">
                                        {{ $contractor['reminders_sent'] }} {{ Str::plural('time', $contractor['reminders_sent']) }}
                                    </flux:badge>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                        This month
                                    </span>
                                @else
                                    <flux:badge color="zinc" size="sm" inset="top bottom">
                                        No reminders
                                    </flux:badge>
                                @endif
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="center">
                            <div class="flex items-center justify-center gap-2">
                                <flux:button variant="ghost" size="sm" icon="bell" wire:click="openRemindModal('{{ $contractor['clab_no'] }}')">
                                    Remind
                                </flux:button>
                                <flux:button variant="ghost" size="sm" icon="eye" href="{{ route('admin.missing-submissions.detail', $contractor['clab_no']) }}" wire:navigate>
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

    <!-- Remind Modal -->
    @if($showRemindModal && $selectedContractor)
        <flux:modal wire:model="showRemindModal" class="min-w-[700px] max-h-[90vh]">
            <div class="space-y-4">
                <div>
                    <h2 class="text-xl font-bold text-zinc-900 dark:text-zinc-100">
                        Send Reminder
                    </h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">
                        Send a reminder to contractor about pending payroll submission
                    </p>
                </div>

                <!-- Contractor Info -->
                <flux:card class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                    <div class="flex items-center gap-3">
                        <flux:icon.building-office class="size-8 text-blue-600 dark:text-blue-400" />
                        <div>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-zinc-100">{{ $selectedContractor['name'] }}</p>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400">CLAB No: {{ $selectedContractor['clab_no'] }}</p>
                            <div class="flex gap-3 mt-1">
                                @if($selectedContractor['email'])
                                    <div class="flex items-center gap-1 text-xs text-zinc-600 dark:text-zinc-400">
                                        <flux:icon.envelope class="size-3" />
                                        <span>{{ $selectedContractor['email'] }}</span>
                                    </div>
                                @endif
                                @if($selectedContractor['phone'])
                                    <div class="flex items-center gap-1 text-xs text-zinc-600 dark:text-zinc-400">
                                        <flux:icon.phone class="size-3" />
                                        <span>{{ $selectedContractor['phone'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </flux:card>

                <!-- Pending Info -->
                <div class="rounded-lg bg-orange-50 dark:bg-orange-900/20 p-4 border border-orange-200 dark:border-orange-800">
                    <div class="flex items-center gap-2">
                        <flux:icon.exclamation-triangle class="size-5 text-orange-600 dark:text-orange-400" />
                        <div>
                            <p class="text-sm font-medium text-orange-900 dark:text-orange-100">
                                {{ $selectedContractor['active_workers'] }} of {{ $selectedContractor['total_workers'] }} workers not submitted
                            </p>
                            <p class="text-xs text-orange-700 dark:text-orange-300">
                                Pending for {{ now()->format('F Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Past Reminders History -->
                @if(count($pastReminders) > 0)
                    <div>
                        <h3 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 mb-2">
                            Past Reminders ({{ count($pastReminders) }})
                        </h3>
                        <div class="space-y-2 max-h-40 overflow-y-auto rounded-lg border border-zinc-200 dark:border-zinc-700 p-2 bg-zinc-50 dark:bg-zinc-800/50">
                            @foreach($pastReminders as $reminder)
                                <div class="bg-white dark:bg-zinc-900 rounded-lg p-2 border border-zinc-200 dark:border-zinc-700">
                                    <div class="flex items-center gap-2 text-xs">
                                        <flux:icon.clock class="size-3 text-zinc-500 dark:text-zinc-400" />
                                        <span class="font-medium text-zinc-900 dark:text-zinc-100">
                                            {{ \Carbon\Carbon::parse($reminder['created_at'])->format('M d, Y h:i A') }}
                                        </span>
                                        <span class="text-zinc-400">•</span>
                                        <flux:icon.user class="size-3 text-zinc-500 dark:text-zinc-400" />
                                        <span class="text-zinc-600 dark:text-zinc-400">
                                            {{ $reminder['sent_by'] ?? 'System' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Message -->
                <div>
                    <flux:textarea
                        wire:model="reminderMessage"
                        label="Reminder Message"
                        rows="6"
                        resize="none"
                        placeholder="Enter your reminder message..."
                    />
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                    <flux:button wire:click="closeRemindModal" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="sendReminder" variant="primary" icon="paper-airplane">
                        Send Reminder
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
