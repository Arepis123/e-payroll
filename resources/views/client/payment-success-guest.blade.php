<x-layouts.auth.simple :title="__('Payment Successful')">
    <div class="flex flex-col items-center justify-center gap-6">
        <div class="text-center">
            <div class="mb-4 flex justify-center">
                <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-4">
                    <flux:icon.check-circle class="size-8 text-green-600 dark:text-green-400" />
                </div>
            </div>

            <h1 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 mb-2">Payment Successful!</h1>
            <p class="text-base text-zinc-600 dark:text-zinc-400 mb-6">
                Your payroll payment has been processed successfully.
            </p>
        </div>

        <flux:card class="p-6 dark:bg-zinc-900 rounded-lg w-full">
            <h2 class="text-base font-semibold text-zinc-900 dark:text-zinc-100 mb-4">Payment Details</h2>

            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Period</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $submission->month_year }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Workers</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $submission->total_workers }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Amount Paid</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        RM {{ number_format($submission->payment->amount, 2) }}
                    </span>
                </div>

                @if($submission->has_penalty)
                <div class="flex justify-between text-red-600 dark:text-red-400">
                    <span class="text-sm">Late Payment Penalty</span>
                    <span class="text-sm font-medium">+ RM {{ number_format($submission->penalty_amount, 2) }}</span>
                </div>
                @endif

                <div class="flex justify-between pt-3 border-t border-zinc-200 dark:border-zinc-700">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Transaction ID</span>
                    <span class="text-xs font-mono text-zinc-900 dark:text-zinc-100">
                        {{ $submission->payment->transaction_id ?? 'N/A' }}
                    </span>
                </div>

                <div class="flex justify-between">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Payment Date</span>
                    <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                        {{ $submission->payment->completed_at ? $submission->payment->completed_at->format('F d, Y h:i A') : now()->format('F d, Y h:i A') }}
                    </span>
                </div>
            </div>
        </flux:card>

        <div class="flex flex-col gap-3 w-full">
            <flux:button variant="primary" href="{{ route('login') }}" class="w-full">
                <flux:icon.log-in class="size-4" />
                Login to View Dashboard
            </flux:button>
            <p class="text-xs text-center text-zinc-500 dark:text-zinc-400">
                Your payment has been confirmed. Please login to access your account.
            </p>
        </div>
    </div>
</x-layouts.auth.simple>
