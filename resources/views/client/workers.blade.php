<x-layouts.app :title="__('My Workers')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-zinc-100">My Workers</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Manage workers assigned to your company</p>
            </div>
        </div>

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

        <!-- Filters and Search (Accordion) -->
        {{-- Debug: Status={{ $statusFilter ?? 'null' }}, Country={{ $countryFilter ?? 'null' }}, Position={{ $positionFilter ?? 'null' }} --}}
        {{-- Countries: {{ $countries->pluck('cty_code')->implode(', ') }} --}}
        {{-- Positions: {{ $positions->pluck('trade_code')->implode(', ') }} --}}
        <div>
            <button type="button" onclick="toggleFilters()" class="w-full flex items-center justify-between p-1 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                <div class="flex items-center gap-2 flex-wrap">
                    <div class="flex items-center gap-3">
                        <flux:icon.funnel class="size-5 text-zinc-600 dark:text-zinc-400" />
                        <h3 class="text-base font-semibold text-zinc-900 dark:text-zinc-100">Search & Filters</h3>
                    </div>
                    @if($search)
                        <flux:badge color="zinc" size="sm">Search: "{{ Str::limit($search, 20) }}"</flux:badge>
                    @endif
                    @if(isset($statusFilter) && $statusFilter !== 'all')
                        <flux:badge color="zinc" size="sm">{{ ucfirst($statusFilter) }}</flux:badge>
                    @endif
                    @if(isset($countryFilter) && $countryFilter !== 'all' && $countryFilter)
                        @php
                            $selectedCountry = $countries->first(function($countryItem) use ($countryFilter) {
                                return $countryItem && $countryItem->cty_code === $countryFilter;
                            });
                            $countryName = $selectedCountry ? $selectedCountry->cty_desc : $countryFilter;
                        @endphp
                        <flux:badge color="zinc" size="sm">{{ $countryName }}</flux:badge>
                    @endif
                    @if(isset($positionFilter) && $positionFilter !== 'all' && $positionFilter)
                        @php
                            $selectedPosition = $positions->first(function($positionItem) use ($positionFilter) {
                                return $positionItem && $positionItem->trade_code === $positionFilter;
                            });
                            $positionName = $selectedPosition ? $selectedPosition->trade_desc : $positionFilter;
                        @endphp
                        <flux:badge color="zinc" size="sm">{{ $positionName }}</flux:badge>
                    @endif
                </div>
                <flux:icon.chevron-down id="filter-chevron" class="size-5 text-zinc-600 dark:text-zinc-400 transition-transform duration-200" />
            </button>

            <div id="filter-content" class="border-t border-zinc-200 dark:border-zinc-700 mt-2" style="display: none;">
                <form method="GET" action="{{ route('client.workers') }}" class="p-6" onsubmit="
                    const countrySelect = document.getElementById('country-select');
                    console.log('=== FORM SUBMISSION ===');
                    console.log('Status value:', this.status.value);
                    console.log('Country value:', this.country.value);
                    console.log('Country selectedIndex:', countrySelect.selectedIndex);
                    console.log('Country selected option:', countrySelect.options[countrySelect.selectedIndex]);
                    console.log('Position value:', this.position.value);
                    console.log('Form data:', new FormData(this));
                    for (let pair of new FormData(this).entries()) {
                        console.log(pair[0] + ': ' + pair[1]);
                    }
                    return true;
                ">
                    <!-- Search Bar -->
                    <div class="mb-4">
                        <flux:input
                            name="search"
                            value="{{ $search ?? '' }}"
                            placeholder="Search by name, passport number, or worker ID..."
                            icon="magnifying-glass"
                            size="lg"
                        />
                    </div>

                    <!-- Filters Row -->
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Status</label>
                                <select id="status-select" name="status" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 px-3 py-2">
                                    <option value="all" {{ (!$statusFilter || $statusFilter === 'all') ? 'selected' : '' }}>All Status</option>
                                    <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $statusFilter === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Country</label>
                                <select id="country-select" name="country" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 px-3 py-2">
                                    <option value="all" {{ (!$countryFilter || $countryFilter === 'all') ? 'selected' : '' }}>All Country</option>
                                    @foreach($countries as $countryItem)
                                        <option value="{{ $countryItem->cty_code }}" {{ $countryFilter === $countryItem->cty_code ? 'selected' : '' }}>{{ $countryItem->cty_desc }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-zinc-700 dark:text-zinc-300">Position</label>
                                <select id="position-select" name="position" class="w-full rounded-lg border border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100 px-3 py-2">
                                    <option value="all" {{ (!$positionFilter || $positionFilter === 'all') ? 'selected' : '' }}>All Position</option>
                                    @foreach($positions as $positionItem)
                                        <option value="{{ $positionItem->trade_code }}" {{ $positionFilter === $positionItem->trade_code ? 'selected' : '' }}>{{ $positionItem->trade_desc }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-2 sm:flex-shrink-0">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Apply
                            </button>
                            <a href="{{ route('client.workers') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>    
        </div>    
        
        <script>
            function toggleFilters() {
                const content = document.getElementById('filter-content');
                const chevron = document.getElementById('filter-chevron');

                if (content.style.display === 'none') {
                    content.style.display = 'block';
                    chevron.style.transform = 'rotate(180deg)';
                } else {
                    content.style.display = 'none';
                    chevron.style.transform = 'rotate(0deg)';
                }
            }

            // Auto-expand if filters are active
            document.addEventListener('DOMContentLoaded', function() {
                const hasActiveFilters = {{ ($search || ($statusFilter && $statusFilter !== 'all') || ($countryFilter && $countryFilter !== 'all') || ($positionFilter && $positionFilter !== 'all')) ? 'true' : 'false' }};

                if (hasActiveFilters) {
                    toggleFilters();
                }
            });
        </script>

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
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Passport Expiry</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Permit Expiry</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Country</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Position</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Basic Salary</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Status</th>
                            <th class="pb-3 text-left text-xs font-medium text-zinc-600 dark:text-zinc-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @forelse($workers as $worker)
                            @php
                                $passportExpired = $worker->wkr_passexp && $worker->wkr_passexp->isPast();
                                $passportExpiringSoon = $worker->wkr_passexp && $worker->wkr_passexp->isFuture() && now()->diffInDays($worker->wkr_passexp, false) <= 90;
                                $permitExpired = $worker->wkr_permitexp && $worker->wkr_permitexp->isPast();
                                $permitExpiringSoon = $worker->wkr_permitexp && $worker->wkr_permitexp->isFuture() && now()->diffInDays($worker->wkr_permitexp, false) <= 60;
                            @endphp
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                <td class="py-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $worker->wkr_id }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <flux:avatar size="sm" name="{{ $worker->name }}" />
                                        <span class="text-sm text-zinc-900 dark:text-zinc-100">{{ $worker->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-sm text-zinc-600 dark:text-zinc-400">{{ $worker->ic_number }}</td>
                                <td class="py-3 text-sm {{ $passportExpired ? 'text-red-600 dark:text-red-400' : ($passportExpiringSoon ? 'text-orange-600 dark:text-orange-400' : 'text-zinc-900 dark:text-zinc-100') }}">
                                    @if($worker->wkr_passexp)
                                        {{ $worker->wkr_passexp->format('M d, Y') }}
                                        @if($passportExpired)
                                            <span class="text-xs">(Expired)</span>
                                        @elseif($passportExpiringSoon)
                                            <span class="text-xs">(Soon)</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 text-sm {{ $permitExpired ? 'text-red-600 dark:text-red-400' : ($permitExpiringSoon ? 'text-orange-600 dark:text-orange-400' : 'text-zinc-900 dark:text-zinc-100') }}">
                                    @if($worker->wkr_permitexp)
                                        {{ $worker->wkr_permitexp->format('M d, Y') }}
                                        @if($permitExpired)
                                            <span class="text-xs">(Expired)</span>
                                        @elseif($permitExpiringSoon)
                                            <span class="text-xs">(Soon)</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $worker->country->cty_desc ?? '-' }}</td>
                                <td class="py-3 text-sm text-zinc-900 dark:text-zinc-100">{{ $worker->workTrade->trade_desc ?? '-' }}</td>
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
                                <td colspan="10" class="py-8 text-center text-zinc-600 dark:text-zinc-400">
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
