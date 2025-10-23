<?php

namespace App\Livewire\Admin;

use App\Models\PayrollSubmission;
use App\Models\User;
use App\Models\ContractWorker;
use App\Models\Contractor;
use Livewire\Component;

class MissingSubmissions extends Component
{
    public $missingContractors = [];

    public function mount()
    {
        $this->loadMissingContractors();
    }

    protected function loadMissingContractors()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // OPTIMIZATION 1: Get all data in one query per connection
        $contractorsWithActiveWorkers = ContractWorker::active()
            ->distinct()
            ->pluck('con_ctr_clab_no')
            ->unique();

        $contractorsWithSubmission = PayrollSubmission::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->pluck('contractor_clab_no')
            ->unique();

        $missingContractors = $contractorsWithActiveWorkers->diff($contractorsWithSubmission);

        if ($missingContractors->isEmpty()) {
            $this->missingContractors = collect();
            return;
        }

        // OPTIMIZATION 2: Batch load all users at once (1 query instead of N)
        $users = User::whereIn('contractor_clab_no', $missingContractors)
            ->where('role', 'client')
            ->get()
            ->keyBy('contractor_clab_no');

        // OPTIMIZATION 3: Batch load all contractors at once (1 query instead of N)
        $contractors = Contractor::whereIn('ctr_clab_no', $missingContractors)
            ->get()
            ->keyBy('ctr_clab_no');

        // OPTIMIZATION 4: Batch count active workers (1 query instead of N)
        $workerCounts = ContractWorker::active()
            ->whereIn('con_ctr_clab_no', $missingContractors)
            ->select('con_ctr_clab_no', \DB::raw('COUNT(*) as count'))
            ->groupBy('con_ctr_clab_no')
            ->pluck('count', 'con_ctr_clab_no');

        // Build result set
        $result = collect();
        foreach ($missingContractors as $clabNo) {
            $activeWorkersCount = $workerCounts->get($clabNo, 0);

            if ($activeWorkersCount > 0) {
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
                    'active_workers' => $activeWorkersCount,
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
