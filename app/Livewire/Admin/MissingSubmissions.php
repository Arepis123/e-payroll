<?php

namespace App\Livewire\Admin;

use App\Models\PayrollSubmission;
use App\Models\PayrollWorker;
use App\Models\PayrollReminder;
use App\Models\User;
use App\Models\ContractWorker;
use App\Models\Contractor;
use App\Mail\PayrollReminderMail;
use Flux\Flux;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Livewire\Component;

class MissingSubmissions extends Component
{
    public $missingContractors = [];
    public $showRemindModal = false;
    public $selectedContractor = null;
    public $reminderMessage = '';
    public $pastReminders;

    public function mount()
    {
        $this->pastReminders = collect();
        $this->loadMissingContractors();
    }

    public function refresh()
    {
        $previousCount = $this->missingContractors->count();

        $this->loadMissingContractors();

        $newCount = $this->missingContractors->count();

        if ($previousCount === 0 && $newCount === 0) {
            Flux::toast(
                variant: 'success',
                heading: 'Data refreshed',
                text: 'All contractors have submitted their payroll.'
            );
        } elseif ($newCount < $previousCount) {
            $difference = $previousCount - $newCount;
            Flux::toast(
                variant: 'success',
                heading: 'Data refreshed',
                text: "{$difference} " . \Illuminate\Support\Str::plural('contractor', $difference) . " submitted since last refresh!"
            );
        } elseif ($newCount > $previousCount) {
            $difference = $newCount - $previousCount;
            Flux::toast(
                variant: 'warning',
                heading: 'Data refreshed',
                text: "{$difference} new " . \Illuminate\Support\Str::plural('contractor', $difference) . " with missing submissions."
            );
        } else {
            Flux::toast(
                variant: 'info',
                heading: 'Data refreshed',
                text: 'No changes. Still ' . $newCount . ' ' . \Illuminate\Support\Str::plural('contractor', $newCount) . ' with missing submissions.'
            );
        }
    }

    public function openRemindModal($clabNo)
    {
        $this->selectedContractor = collect($this->missingContractors)->firstWhere('clab_no', $clabNo);

        if ($this->selectedContractor) {
            // Load past reminders for this contractor (current month/year)
            $this->pastReminders = PayrollReminder::where('contractor_clab_no', $clabNo)
                ->where('month', now()->month)
                ->where('year', now()->year)
                ->orderBy('created_at', 'desc')
                ->get();

            // Set default reminder message
            $this->reminderMessage = "Dear {$this->selectedContractor['name']},\n\n";
            $this->reminderMessage .= "This is a friendly reminder that you have {$this->selectedContractor['active_workers']} worker(s) pending payroll submission for " . now()->format('F Y') . ".\n\n";
            $this->reminderMessage .= "Please submit the payroll at your earliest convenience to avoid any delays.\n\n";
            $this->reminderMessage .= "Thank you for your cooperation.\n\n";
            $this->reminderMessage .= "Best regards,\ne-Salary CLAB System";

            $this->showRemindModal = true;
        }
    }

    public function closeRemindModal()
    {
        $this->showRemindModal = false;
        $this->selectedContractor = null;
        $this->reminderMessage = '';
        $this->pastReminders = collect();
    }

    public function export()
    {
        // Check if there are missing contractors
        if ($this->missingContractors->isEmpty()) {
            Flux::toast(
                variant: 'warning',
                heading: 'No data to export',
                text: 'There are no missing submissions for the current period.'
            );
            return;
        }

        // Generate CSV content
        $csvContent = $this->generateCsv();

        // Generate filename with current date
        $filename = 'missing_submissions_' . now()->format('Y-m-d_His') . '.csv';

        // Return download response
        return Response::streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    protected function generateCsv()
    {
        $currentMonth = now()->format('F Y');

        // CSV Header
        $csv = "Missing Payroll Submissions Report\n";
        $csv .= "Period: {$currentMonth}\n";
        $csv .= "Generated: " . now()->format('d M Y, h:i A') . "\n\n";

        // Column headers
        $csv .= "No,CLAB No,Contractor Name,Email,Phone,Active Workers Not Submitted,Total Workers,Workers Submitted,Reminders Sent,Status\n";

        // Data rows
        foreach ($this->missingContractors as $index => $contractor) {
            $csv .= ($index + 1) . ',';
            $csv .= '"' . $contractor['clab_no'] . '",';
            $csv .= '"' . str_replace('"', '""', $contractor['name']) . '",';
            $csv .= '"' . ($contractor['email'] ?? 'N/A') . '",';
            $csv .= '"' . ($contractor['phone'] ?? 'N/A') . '",';
            $csv .= $contractor['active_workers'] . ',';
            $csv .= $contractor['total_workers'] . ',';
            $csv .= ($contractor['total_workers'] - $contractor['active_workers']) . ',';
            $csv .= $contractor['reminders_sent'] . ',';
            $csv .= $contractor['reminders_sent'] > 0 ? 'Reminded' : 'Not Reminded';
            $csv .= "\n";
        }

        // Summary
        $csv .= "\nSummary\n";
        $csv .= "Total Contractors Missing Submission," . $this->missingContractors->count() . "\n";
        $csv .= "Total Workers Not Submitted," . $this->missingContractors->sum('active_workers') . "\n";
        $csv .= "Total Workers Submitted," . $this->missingContractors->sum(function ($c) {
            return $c['total_workers'] - $c['active_workers'];
        }) . "\n";

        return $csv;
    }

    public function sendReminder()
    {
        // Validate
        if (!$this->selectedContractor || empty($this->reminderMessage)) {
            Flux::toast(variant: 'danger', text: 'Cannot send reminder without a message.');
            return;
        }

        // Validate email exists
        if (empty($this->selectedContractor['email'])) {
            Flux::toast(variant: 'danger', text: 'Cannot send reminder: No email address found for this contractor.');
            return;
        }

        try {
            // Send email
            Mail::to($this->selectedContractor['email'])->send(
                new PayrollReminderMail(
                    $this->selectedContractor['name'],
                    $this->selectedContractor['clab_no'],
                    $this->selectedContractor['active_workers'],
                    $this->selectedContractor['total_workers'],
                    now()->format('F Y'),
                    $this->reminderMessage
                )
            );

            // Save reminder record to database
            PayrollReminder::create([
                'contractor_clab_no' => $this->selectedContractor['clab_no'],
                'contractor_name' => $this->selectedContractor['name'],
                'contractor_email' => $this->selectedContractor['email'],
                'month' => now()->month,
                'year' => now()->year,
                'message' => $this->reminderMessage,
                'sent_by' => auth()->user()->name ?? 'System',
            ]);

            Flux::toast(
                variant: 'success',
                heading: 'Reminder sent!',
                text: "Email sent to {$this->selectedContractor['name']} ({$this->selectedContractor['email']})"
            );
        } catch (\Exception $e) {
            Flux::toast(variant: 'danger', heading: 'Failed to send', text: $e->getMessage());
        }

        $this->closeRemindModal();
    }

    protected function loadMissingContractors()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Get all contractors with active workers
        $contractorsWithActiveWorkers = ContractWorker::active()
            ->distinct()
            ->pluck('con_ctr_clab_no')
            ->unique();

        // Count total active workers per contractor
        $totalActiveWorkers = ContractWorker::active()
            ->select('con_ctr_clab_no', \DB::raw('COUNT(*) as count'))
            ->groupBy('con_ctr_clab_no')
            ->pluck('count', 'con_ctr_clab_no');

        // Get submitted worker IDs for current month
        $submittedWorkerIds = PayrollWorker::whereHas('payrollSubmission', function ($query) use ($currentMonth, $currentYear) {
                $query->where('month', $currentMonth)
                      ->where('year', $currentYear);
            })
            ->pluck('worker_id')
            ->unique();

        // Count unsubmitted workers per contractor
        $unsubmittedWorkerCounts = ContractWorker::active()
            ->whereNotIn('con_wkr_id', $submittedWorkerIds)
            ->select('con_ctr_clab_no', \DB::raw('COUNT(*) as count'))
            ->groupBy('con_ctr_clab_no')
            ->pluck('count', 'con_ctr_clab_no');

        // Find contractors with unsubmitted workers
        $contractorsWithMissingWorkers = $unsubmittedWorkerCounts->keys();

        if ($contractorsWithMissingWorkers->isEmpty()) {
            $this->missingContractors = collect();
            return;
        }

        // Batch load all users at once
        $users = User::whereIn('contractor_clab_no', $contractorsWithMissingWorkers)
            ->where('role', 'client')
            ->get()
            ->keyBy('contractor_clab_no');

        // Batch load all contractors at once
        $contractors = Contractor::whereIn('ctr_clab_no', $contractorsWithMissingWorkers)
            ->get()
            ->keyBy('ctr_clab_no');

        // Get reminder counts for current month/year
        $reminderCounts = PayrollReminder::whereIn('contractor_clab_no', $contractorsWithMissingWorkers)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->select('contractor_clab_no', \DB::raw('COUNT(*) as count'))
            ->groupBy('contractor_clab_no')
            ->pluck('count', 'contractor_clab_no');

        // Build result set
        $result = collect();
        foreach ($contractorsWithMissingWorkers as $clabNo) {
            $unsubmittedCount = $unsubmittedWorkerCounts->get($clabNo, 0);
            $totalCount = $totalActiveWorkers->get($clabNo, 0);

            if ($unsubmittedCount > 0) {
                $user = $users->get($clabNo);
                $contractor = $contractors->get($clabNo);

                $result->push([
                    'clab_no' => $clabNo,
                    'name' => $user
                        ? ($user->company_name ?? $user->name)
                        : ($contractor ? $contractor->ctr_comp_name : 'Contractor ' . $clabNo),
                    'email' => $user
                        ? $user->email
                        : ($contractor ? $contractor->ctr_email : null),
                    'phone' => $user
                        ? $user->phone
                        : ($contractor ? ($contractor->ctr_contact_mobileno ?? $contractor->ctr_telno) : null),
                    'active_workers' => $unsubmittedCount,
                    'total_workers' => $totalCount,
                    'reminders_sent' => $reminderCounts->get($clabNo, 0),
                ]);
            }
        }

        $this->missingContractors = $result->sortBy('name');
    }

    public function render()
    {
        return view('livewire.admin.missing-submissions');
    }
}
