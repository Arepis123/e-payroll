<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\ContractWorkerService;
use Illuminate\Http\Request;

class DashboardController extends Controller
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

        // If user doesn't have a CLAB number or username, show error
        if (!$clabNo) {
            return view('client.dashboard', [
                'error' => 'No contractor identifier assigned to your account. Please contact administrator.',
                'workers' => collect([]),
                'recentWorkers' => collect([]),
                'stats' => [
                    'total_workers' => 0,
                    'active_workers' => 0,
                    'expiring_soon' => 0,
                ],
                'expiringContracts' => collect([]),
            ]);
        }

        // Get contracted workers for this contractor
        $workers = $this->contractWorkerService->getContractedWorkers($clabNo);

        // Get active contracts for this contractor
        $activeContracts = $this->contractWorkerService->getActiveContractsByContractor($clabNo);

        // Get contracts expiring soon (within 30 days)
        $allExpiringContracts = $this->contractWorkerService->getExpiringContracts(30);

        // Filter expiring contracts for this contractor only
        $expiringContracts = $allExpiringContracts->filter(function($contract) use ($clabNo) {
            return $contract->con_ctr_clab_no === $clabNo;
        });

        // Calculate statistics
        $stats = [
            'total_workers' => $workers->count(),
            'active_workers' => $activeContracts->count(),
            'expiring_soon' => $expiringContracts->count(),
        ];

        // Get recent workers (limit to 4 for dashboard)
        $recentWorkers = $workers->take(4);

        return view('client.dashboard', compact('workers', 'recentWorkers', 'stats', 'expiringContracts'));
    }
}
