<x-layouts.app :title="__('My Workers')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">My Workers</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Manage workers assigned to your company</p>
            </div>
        </div>

        <!-- Filters and Search -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <form method="GET" action="{{ route('client.workers') }}">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="md:col-span-2">
                        <flux:input
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Search by name, passport number, or worker ID..."
                            icon="magnifying-glass"
                        />
                    </div>
                    <div>
                        <flux:select name="status">
                            <flux:select.option value="all" selected="{{ !isset($statusFilter) || $statusFilter === 'all' }}">All Status</flux:select.option>
                            <flux:select.option value="active" selected="{{ isset($statusFilter) && $statusFilter === 'active' }}">Active</flux:select.option>
                            <flux:select.option value="inactive" selected="{{ isset($statusFilter) && $statusFilter === 'inactive' }}">Inactive</flux:select.option>
                        </flux:select>
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <flux:button type="submit" variant="primary">Apply Filters</flux:button>
                    <flux:button type="button" variant="ghost" href="{{ route('client.workers') }}">Clear</flux:button>
                </div>
            </form>
        </flux:card>

        <!-- Statistics Cards -->
        <div class="grid gap-4 md:grid-cols-4">
            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Total Workers</p>
                        <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">{{ $stats['total_workers'] }}</p>
                    </div>
                    <flux:icon.users class="size-8 text-blue-600 dark:text-blue-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Active</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active_workers'] }}</p>
                    </div>
                    <flux:icon.check-circle class="size-8 text-green-600 dark:text-green-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Inactive</p>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['inactive_workers'] }}</p>
                    </div>
                    <flux:icon.clock class="size-8 text-orange-600 dark:text-orange-400" />
                </div>
            </flux:card>

            <flux:card class="space-y-2 p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400">Avg Salary</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            @if($stats['average_salary'] > 0)
                                RM {{ number_format($stats['average_salary'], 2) }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <flux:icon.wallet class="size-8 text-purple-600 dark:text-purple-400" />
                </div>
            </flux:card>
        </div>

        <!-- Workers List -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900 rounded-lg">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">All Workers</h2>
                <div class="flex gap-2">
                    <flux:button variant="ghost" size="sm">
                        <flux:icon.arrow-down-tray class="size-4" />
                        Export
                    </flux:button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700">
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Employee ID</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Name</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Passport Number</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Country</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Position</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Basic Salary</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($workers as $worker)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->wkr_id }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <flux:avatar size="sm" name="{{ $worker->name }}" />
                                        <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $worker->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $worker->ic_number }}</td>
                                <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $worker->country->cty_desc ?? '-' }}</td>
                                <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $worker->position ?? '-' }}</td>
                                <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                    @if($worker->basic_salary)
                                        RM {{ number_format($worker->basic_salary, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($worker->contract_info && $worker->contract_info->isActive())
                                        <flux:badge color="green" size="sm">Active</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Inactive</flux:badge>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <flux:dropdown>
                                        <flux:button variant="ghost" size="sm" icon="ellipsis-horizontal" />
                                        <flux:menu>
                                            <flux:menu.item icon="eye" href="{{ route('client.workers.show', $worker->wkr_id) }}">View Details</flux:menu.item>
                                            <flux:menu.item icon="document-text">View Payslips</flux:menu.item>
                                        </flux:menu>
                                    </flux:dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-zinc-600 dark:text-zinc-400">
                                    No workers found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pagination['total'] > 0)
                <div class="mt-4 flex items-center justify-between border-t border-zinc-200 dark:border-zinc-700 pt-4">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total'] }} results
                    </p>
                    <div class="flex gap-2">
                        @if($pagination['current_page'] > 1)
                            <flux:button variant="ghost" size="sm" href="{{ route('client.workers', array_merge(request()->query(), ['page' => $pagination['current_page'] - 1])) }}">Previous</flux:button>
                        @else
                            <flux:button variant="ghost" size="sm" disabled>Previous</flux:button>
                        @endif

                        @for($i = 1; $i <= $pagination['last_page']; $i++)
                            @if($i == $pagination['current_page'])
                                <flux:button variant="primary" size="sm">{{ $i }}</flux:button>
                            @else
                                <flux:button variant="ghost" size="sm" href="{{ route('client.workers', array_merge(request()->query(), ['page' => $i])) }}">{{ $i }}</flux:button>
                            @endif
                        @endfor

                        @if($pagination['current_page'] < $pagination['last_page'])
                            <flux:button variant="ghost" size="sm" href="{{ route('client.workers', array_merge(request()->query(), ['page' => $pagination['current_page'] + 1])) }}">Next</flux:button>
                        @else
                            <flux:button variant="ghost" size="sm" disabled>Next</flux:button>
                        @endif
                    </div>
                </div>
            @endif
        </flux:card>
    </div>
</x-layouts.app>
