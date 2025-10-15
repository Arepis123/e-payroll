<?php

namespace App\Services;

use App\Models\ContractWorker;
use App\Models\Worker;
use App\Models\Contractor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ContractWorkerService
{
    /**
     * Cache TTL in seconds (1 hour by default)
     */
    protected int $cacheTTL = 3600;

    /**
     * Get all active contracts with caching
     */
    public function getActiveContracts(): Collection
    {
        return Cache::remember(
            'contract_workers:active',
            $this->cacheTTL,
            fn() => ContractWorker::active()
                ->with(['contractor', 'worker'])
                ->get()
        );
    }

    /**
     * Get contracts by contractor CLAB number
     */
    public function getContractsByContractor(string $clabNo): Collection
    {
        return Cache::remember(
            "contract_workers:contractor:{$clabNo}",
            $this->cacheTTL,
            fn() => ContractWorker::byContractor($clabNo)
                ->with('worker')
                ->get()
        );
    }

    /**
     * Get active contracts by contractor CLAB number
     */
    public function getActiveContractsByContractor(string $clabNo): Collection
    {
        return Cache::remember(
            "contract_workers:contractor:{$clabNo}:active",
            $this->cacheTTL,
            fn() => ContractWorker::byContractor($clabNo)
                ->active()
                ->with('worker')
                ->get()
        );
    }

    /**
     * Get contracted workers for a specific contractor
     * Returns only workers that have active contracts
     */
    public function getContractedWorkers(string $clabNo): Collection
    {
        return Cache::remember(
            "contracted_workers:contractor:{$clabNo}",
            $this->cacheTTL,
            function() use ($clabNo) {
                $contractWorkerIds = ContractWorker::byContractor($clabNo)
                    ->active()
                    ->pluck('con_wkr_id');

                return Worker::whereIn('wkr_id', $contractWorkerIds)
                    ->with('country')
                    ->get()
                    ->map(function($worker) use ($clabNo) {
                        // Attach contract info to each worker
                        $contract = ContractWorker::where('con_wkr_id', $worker->wkr_id)
                            ->where('con_ctr_clab_no', $clabNo)
                            ->active()
                            ->first();

                        $worker->contract_info = $contract;
                        return $worker;
                    });
            }
        );
    }

    /**
     * Get all contractors that have active contracts in the system
     */
    public function getContractorsWithActiveContracts(): Collection
    {
        return Cache::remember(
            'contracted_contractors:active',
            $this->cacheTTL,
            function() {
                $clabNumbers = ContractWorker::active()
                    ->distinct()
                    ->pluck('con_ctr_clab_no');

                return Contractor::whereIn('ctr_clab_no', $clabNumbers)->get();
            }
        );
    }

    /**
     * Get contract by ID
     */
    public function getContract(int $contractId): ?ContractWorker
    {
        return Cache::remember(
            "contract_worker:{$contractId}",
            $this->cacheTTL,
            fn() => ContractWorker::with(['contractor', 'worker'])->find($contractId)
        );
    }

    /**
     * Get contracts expiring within specified days
     */
    public function getExpiringContracts(int $days = 30): Collection
    {
        return Cache::remember(
            "contract_workers:expiring:{$days}",
            $this->cacheTTL,
            function() use ($days) {
                $endDate = now()->addDays($days)->toDateString();

                return ContractWorker::where('con_end', '>=', now()->toDateString())
                    ->where('con_end', '<=', $endDate)
                    ->with(['contractor', 'worker'])
                    ->orderBy('con_end')
                    ->get();
            }
        );
    }

    /**
     * Get statistics about contracts
     */
    public function getContractStatistics(): array
    {
        return Cache::remember(
            'contract_workers:statistics',
            $this->cacheTTL,
            function() {
                $total = ContractWorker::count();
                $active = ContractWorker::active()->count();
                $expired = ContractWorker::expired()->count();
                $expiringIn30Days = ContractWorker::active()
                    ->where('con_end', '<=', now()->addDays(30)->toDateString())
                    ->count();

                $contractorCount = ContractWorker::active()
                    ->distinct('con_ctr_clab_no')
                    ->count('con_ctr_clab_no');

                $workerCount = ContractWorker::active()
                    ->distinct('con_wkr_id')
                    ->count('con_wkr_id');

                return [
                    'total_contracts' => $total,
                    'active_contracts' => $active,
                    'expired_contracts' => $expired,
                    'expiring_soon' => $expiringIn30Days,
                    'active_contractors' => $contractorCount,
                    'active_workers' => $workerCount,
                ];
            }
        );
    }

    /**
     * Search contracts by worker name or passport number
     */
    public function searchContracts(string $query): Collection
    {
        $cacheKey = "contract_workers:search:" . md5($query);

        return Cache::remember(
            $cacheKey,
            $this->cacheTTL,
            function() use ($query) {
                // Get matching worker IDs
                $workerIds = Worker::where('wkr_name', 'LIKE', "%{$query}%")
                    ->orWhere('wkr_passno', 'LIKE', "%{$query}%")
                    ->pluck('wkr_id');

                return ContractWorker::whereIn('con_wkr_id', $workerIds)
                    ->orWhere('con_ctr_clab_no', 'LIKE', "%{$query}%")
                    ->with(['contractor', 'worker'])
                    ->get();
            }
        );
    }

    /**
     * Check if a worker has an active contract with a contractor
     */
    public function hasActiveContract(int $workerId, string $clabNo): bool
    {
        return ContractWorker::where('con_wkr_id', $workerId)
            ->where('con_ctr_clab_no', $clabNo)
            ->active()
            ->exists();
    }

    /**
     * Get worker's active contract
     */
    public function getWorkerActiveContract(int $workerId): ?ContractWorker
    {
        return ContractWorker::where('con_wkr_id', $workerId)
            ->active()
            ->with(['contractor', 'worker'])
            ->first();
    }

    /**
     * Invalidate cache for a specific contract
     */
    public function invalidateContractCache(int $contractId): void
    {
        Cache::forget("contract_worker:{$contractId}");
        Cache::forget('contract_workers:active');
        Cache::forget('contract_workers:statistics');

        // Get the contract to invalidate contractor-specific cache
        $contract = ContractWorker::find($contractId);
        if ($contract) {
            Cache::forget("contract_workers:contractor:{$contract->con_ctr_clab_no}");
            Cache::forget("contract_workers:contractor:{$contract->con_ctr_clab_no}:active");
            Cache::forget("contracted_workers:contractor:{$contract->con_ctr_clab_no}");
        }
    }

    /**
     * Invalidate all contract caches
     */
    public function invalidateAllContractCaches(): void
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            Cache::tags(['contract_workers'])->flush();
        } else {
            Cache::forget('contract_workers:active');
            Cache::forget('contract_workers:statistics');
            Cache::forget('contracted_contractors:active');
        }
    }

    /**
     * Set custom cache TTL
     */
    public function setCacheTTL(int $seconds): self
    {
        $this->cacheTTL = $seconds;
        return $this;
    }
}
