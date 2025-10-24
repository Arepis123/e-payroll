<?php

namespace App\Livewire\Admin;

use App\Models\PayrollSubmission;
use App\Models\PayrollWorker;
use App\Models\PayrollReminder;
use App\Models\User;
use App\Models\ContractWorker;
use App\Models\Contractor;
use App\Mail\PayrollReminderMail;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;

class MissingSubmissions extends Component
{
    public $missingContractors = [];
    public $showRemindModal = false;
    public $selectedContractor = null;
    public $reminderMessage = '';
    public $pastReminders = [];

    public function mount()
    {
        $this->loadMissingContractors();
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
                ->get()
                ->toArray();

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
        $this->pastReminders = [];
    }

    public function sendReminder()
    {
        // Validate
        if (!$this->selectedContractor || empty($this->reminderMessage)) {
            session()->flash('error', 'Cannot send reminder without a message.');
            return;
        }

        // Validate email exists
        if (empty($this->selectedContractor['email'])) {
            session()->flash('error', 'Cannot send reminder: No email address found for this contractor.');
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

            session()->flash('success', "Reminder email sent successfully to {$this->selectedContractor['name']} ({$this->selectedContractor['email']})!");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send reminder: ' . $e->getMessage());
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
