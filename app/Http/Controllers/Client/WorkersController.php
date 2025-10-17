<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ContractWorkerService;
use Illuminate\Http\Request;

class WorkersController extends Controller
{
    protected ContractWorkerService $contractWorkerService;

    public function __construct(ContractWorkerService $contractWorkerService)
    {
        $this->contractWorkerService = $contractWorkerService;
    }

    public function index(Request $request)
    {
        // Get contractor CLAB number from authenticated user
        // Use username as fallback for matching with worker database
        $clabNo = $request->user()->contractor_clab_no ?? $request->user()->username;

        // If user doesn't have a CLAB number, show error
        if (!$clabNo) {
            return view('client.workers', [
                'error' => 'No contractor identifier assigned to your account. Please contact administrator.',
                'workers' => collect([]),
                'stats' => [
                    'total_workers' => 0,
                    'active_workers' => 0,
                    'inactive_workers' => 0,
                    'average_salary' => 0,
                ],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 0,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0,
                ],
                'search' => null,
                'statusFilter' => null,
            ]);
        }

        // Get all contracted workers for this contractor
        $allWorkers = $this->contractWorkerService->getContractedWorkers($clabNo);

        // Apply search filter
        $search = $request->input('search');
        if ($search) {
            $allWorkers = $allWorkers->filter(function($worker) use ($search) {
                return str_contains(strtolower($worker->name), strtolower($search)) ||
                       str_contains(strtolower($worker->ic_number), strtolower($search)) ||
                       str_contains(strtolower($worker->wkr_id), strtolower($search));
            });
        }

        // Apply status filter
        $statusFilter = $request->input('status');
        if ($statusFilter && $statusFilter !== 'all') {
            $allWorkers = $allWorkers->filter(function($worker) use ($statusFilter) {
                if ($statusFilter === 'active') {
                    return $worker->contract_info && $worker->contract_info->isActive();
                } elseif ($statusFilter === 'inactive') {
                    return !$worker->contract_info || !$worker->contract_info->isActive();
                }
                return true;
            });
        }

        // Calculate statistics
        $activeWorkers = $allWorkers->filter(function($worker) {
            return $worker->contract_info && $worker->contract_info->isActive();
        });

        $totalSalary = $allWorkers->sum(function($worker) {
            return $worker->basic_salary ?? 0;
        });

        $averageSalary = $allWorkers->count() > 0
            ? $totalSalary / $allWorkers->count()
            : 0;

        $stats = [
            'total_workers' => $allWorkers->count(),
            'active_workers' => $activeWorkers->count(),
            'inactive_workers' => $allWorkers->count() - $activeWorkers->count(),
            'average_salary' => $averageSalary,
        ];

        // Pagination (simple implementation)
        $perPage = 10;
        $currentPage = $request->input('page', 1);
        $total = $allWorkers->count();

        $workers = $allWorkers->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $pagination = [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => ceil($total / $perPage),
            'from' => (($currentPage - 1) * $perPage) + 1,
            'to' => min($currentPage * $perPage, $total),
        ];

        return view('client.workers', compact('workers', 'stats', 'pagination', 'search', 'statusFilter'));
    }

    public function show(Request $request, $workerId)
    {
        // Get contractor CLAB number from authenticated user
        // Use username as fallback for matching with worker database
        $clabNo = $request->user()->contractor_clab_no ?? $request->user()->username;

        if (!$clabNo) {
            abort(403, 'No contractor identifier assigned.');
        }

        // Get all contracted workers and find the specific one
        $workers = $this->contractWorkerService->getContractedWorkers($clabNo);
        $worker = $workers->firstWhere('wkr_id', $workerId);

        if (!$worker) {
            abort(404, 'Worker not found or not assigned to your company.');
        }

        return view('client.workers.show', compact('worker'));
    }
}
