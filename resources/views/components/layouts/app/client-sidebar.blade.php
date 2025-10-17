<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="bg-white dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.header>
                <flux:sidebar.brand
                    href="{{ route('client.dashboard') }}"
                    wire:navigate
                    logo="{{ asset('favicon.svg') }}"
                    logo:dark="{{ asset('favicon.svg') }}"
                    name="e-Salary CLAB"
                />

                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <div class="px-3 py-2 mt-4 in-data-flux-sidebar-collapsed-desktop:hidden">
                    <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">{{ __('CLIENT PORTAL') }}</h3>
                </div>
                <flux:sidebar.item icon="house" :href="route('client.dashboard')" :current="request()->routeIs('client.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:sidebar.item>
                <flux:sidebar.item icon="users" :href="route('client.workers')" :current="request()->routeIs('client.workers')" wire:navigate>{{ __('My Workers') }}</flux:sidebar.item>
                <flux:sidebar.item icon="wallet" :href="route('client.payments')" :current="request()->routeIs('client.payments')" wire:navigate>{{ __('Payments') }}</flux:sidebar.item>
                <flux:sidebar.item icon="document-text" :href="route('client.invoices')" :current="request()->routeIs('client.invoices')" wire:navigate>{{ __('Invoices') }}</flux:sidebar.item>
                <flux:sidebar.item icon="calendar" :href="route('client.timesheet')" :current="request()->routeIs('client.timesheet')" wire:navigate>{{ __('Timesheet') }}</flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="settings" :href="route('settings.profile')" wire:navigate>
                    {{ __('Settings') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <!-- Desktop User Menu -->
            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:sidebar.profile
                    :name="auth()->user()->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A'"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar name="{{ auth()->user() ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A' }}" />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A' }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="start">
                <flux:profile
                    :initials="auth()->user()
                        ? collect(explode(' ', preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name)))
                            ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                            ->implode('')
                        : 'NA'"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                        <flux:avatar class="flex h-full w-full items-center justify-center" name="{{ auth()->user() ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A' }}" />
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
