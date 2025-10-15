# Contract Worker Integration Guide

## ⚠️ Important: Read-Only Access

**This payroll system has READ-ONLY access to the `contract_worker` table.**

Contract data is managed by another system. This payroll system:
- ✅ **Reads** contract data to determine which workers to process
- ✅ **Displays** contract information (start date, end date, period)
- ✅ **Filters** workers based on active contracts
- ❌ **Does NOT** create, update, or delete contract records

---

## Overview

The `contract_worker` table acts as a **junction/pivot table** that defines which contractor-worker relationships are actively used in the payroll system. This table filters the large dataset from the second database to only include relevant contracts.

## Why This Table Exists

The second database contains:
- **5,809 contractors** (all contractors in the system)
- **69,726 workers** (all workers in the system)

**Problem:** Your payroll system doesn't need ALL contractors and ALL workers. You only need specific contractor-worker pairs that have active contracts.

**Solution:** The `contract_worker` table acts as a filter, defining:
- Which contractors are actively using the payroll system
- Which workers are contracted to those contractors
- The contract period and dates

---

## Table Structure

### contract_worker

| Column          | Type         | Description                                    |
|-----------------|--------------|------------------------------------------------|
| `con_id`        | int(11)      | Primary key (auto-increment)                   |
| `con_ctr_clab_no` | varchar(255) | Contractor CLAB number (FK to contractors)   |
| `con_wkr_id`    | int(11)      | Worker ID (FK to workers)                      |
| `con_wkr_passno` | varchar(255) | Worker passport number (redundant)           |
| `con_period`    | int(11)      | Contract period in months                      |
| `con_start`     | date         | Contract start date                            |
| `con_end`       | date         | Contract end date                              |
| `con_created_at`| datetime     | When contract was added to system              |
| `con_created_by`| int(11)      | User ID who created the contract               |

### Example Data

```
con_id: 1
con_ctr_clab_no: CLAB005617
con_wkr_id: 133951
con_wkr_passno: MF895004
con_period: 6 (months)
con_start: 2025-10-15
con_end: 2026-04-14
```

This means:
- Worker #133951 (passport MF895004) is contracted to Contractor CLAB005617
- Contract is for 6 months, starting Oct 15, 2025, ending April 14, 2026
- This worker will appear in the payroll system for this contractor

---

## Models

### ContractWorker Model

**Location:** `app/Models/ContractWorker.php`

```php
use App\Models\ContractWorker;

// Get all active contracts
$contracts = ContractWorker::active()->get();

// Get contracts for specific contractor
$contracts = ContractWorker::byContractor('CLAB005617')->get();

// Get contract with related data
$contract = ContractWorker::with(['contractor', 'worker'])->find(1);

echo $contract->contractor->company_name; // Contractor name
echo $contract->worker->name; // Worker name
echo $contract->period; // Contract period in months
echo $contract->isActive(); // Check if contract is still active
echo $contract->daysRemaining(); // Days until contract ends
```

**Key Methods:**
- `active()` - Scope for active contracts (end date >= today)
- `expired()` - Scope for expired contracts
- `byContractor($clabNo)` - Filter by contractor
- `byWorker($workerId)` - Filter by worker
- `isActive()` - Check if contract is active
- `daysRemaining()` - Get remaining days

### Updated Contractor Model

```php
use App\Models\Contractor;

$contractor = Contractor::find('CLAB005617');

// Get ALL workers from worker_db (not filtered by contract)
$allWorkers = $contractor->workers;

// Get ONLY contracted workers (filtered by contract_worker table)
$contractedWorkers = $contractor->contractedWorkers;

// Get ONLY active contracted workers
$activeWorkers = $contractor->activeContractedWorkers;

// Get contract records
$contracts = $contractor->contracts;
```

### Updated Worker Model

```php
use App\Models\Worker;

$worker = Worker::find(133951);

// Get all contracts for this worker
$contracts = $worker->contracts;

// Get active contract
$activeContract = $worker->activeContract;

// Check if has active contract
if ($worker->hasActiveContract()) {
    echo "Worker has active contract";
}
```

---

## Services

### ContractWorkerService

**Location:** `app/Services/ContractWorkerService.php`

#### Basic Usage

```php
use App\Services\ContractWorkerService;

$service = new ContractWorkerService();

// Get all active contracts
$contracts = $service->getActiveContracts();

// Get contracts by contractor
$contracts = $service->getContractsByContractor('CLAB005617');

// Get active contracts by contractor
$contracts = $service->getActiveContractsByContractor('CLAB005617');

// Get contracted workers for a contractor
$workers = $service->getContractedWorkers('CLAB005617');
// Each worker includes contract_info property

// Get all contractors with active contracts
$contractors = $service->getContractorsWithActiveContracts();

// Get single contract
$contract = $service->getContract(1);

// Get contracts expiring soon
$expiring = $service->getExpiringContracts(30); // Within 30 days

// Get statistics
$stats = $service->getContractStatistics();
/*
Returns:
[
    'total_contracts' => 3,
    'active_contracts' => 3,
    'expired_contracts' => 0,
    'expiring_soon' => 0,
    'active_contractors' => 1,
    'active_workers' => 3,
]
*/

// Search contracts
$results = $service->searchContracts('MF895004');

// Check if worker has active contract
$hasContract = $service->hasActiveContract(133951, 'CLAB005617');

// Get worker's active contract
$contract = $service->getWorkerActiveContract(133951);
```

#### Cache Management

```php
// Invalidate specific contract cache
$service->invalidateContractCache(1);

// Invalidate all contract caches
$service->invalidateAllContractCaches();

// Set custom cache TTL
$service->setCacheTTL(7200)->getActiveContracts(); // 2 hours
```

---

## Usage Examples

### Example 1: Display Contracted Workers for a Contractor

```php
use App\Services\ContractWorkerService;

public function showContractorWorkers($clabNo)
{
    $service = new ContractWorkerService();
    $workers = $service->getContractedWorkers($clabNo);

    foreach ($workers as $worker) {
        echo "Worker: {$worker->name}\n";
        echo "Passport: {$worker->ic_number}\n";

        if ($worker->contract_info) {
            echo "Contract Start: {$worker->contract_info->con_start}\n";
            echo "Contract End: {$worker->contract_info->con_end}\n";
            echo "Days Remaining: {$worker->contract_info->daysRemaining()}\n";
        }
        echo "\n";
    }
}
```

### Example 2: Show Contractors with Active Contracts

```php
use App\Services\ContractWorkerService;

public function activeContractors()
{
    $service = new ContractWorkerService();
    $contractors = $service->getContractorsWithActiveContracts();

    return view('contractors.index', compact('contractors'));
}
```

### Example 3: Contract Expiry Notifications

```php
use App\Services\ContractWorkerService;

public function getExpiringContracts()
{
    $service = new ContractWorkerService();

    // Get contracts expiring in next 30 days
    $expiring = $service->getExpiringContracts(30);

    foreach ($expiring as $contract) {
        echo "Alert: Contract for {$contract->worker->name} ";
        echo "with {$contract->contractor->company_name} ";
        echo "expires in {$contract->daysRemaining()} days\n";
    }
}
```

### Example 4: Dashboard Statistics

```php
use App\Services\ContractWorkerService;

public function dashboard()
{
    $service = new ContractWorkerService();
    $stats = $service->getContractStatistics();

    return view('dashboard', [
        'total_contracts' => $stats['total_contracts'],
        'active_contracts' => $stats['active_contracts'],
        'active_workers' => $stats['active_workers'],
        'expiring_soon' => $stats['expiring_soon'],
    ]);
}
```

### Example 5: Livewire Component for Contractor Workers

```php
<?php

namespace App\Livewire;

use App\Services\ContractWorkerService;
use Livewire\Component;

class ContractorWorkersList extends Component
{
    public $contractorClabNo;
    public $search = '';

    public function render(ContractWorkerService $service)
    {
        $workers = $this->search
            ? $service->searchContracts($this->search)
            : $service->getContractedWorkers($this->contractorClabNo);

        return view('livewire.contractor-workers-list', [
            'workers' => $workers,
        ]);
    }
}
```

---

## Important Concepts

### 1. Two Types of Worker Lists

**All Workers (from worker_db):**
```php
$contractor = Contractor::find('CLAB005617');
$allWorkers = $contractor->workers; // ALL workers with wkr_currentemp = CLAB005617
```

**Contracted Workers (filtered by contract_worker):**
```php
$contractedWorkers = $contractor->contractedWorkers; // ONLY workers in contract_worker
```

**When to use which:**
- Use `contractedWorkers` for **payroll operations** (only contracted workers)
- Use `workers` for **reference** (all workers in the database)

### 2. Active vs All Contracts

**Active Contracts:**
- `con_end >= today`
- Contract is still valid

**All Contracts:**
- Includes expired contracts
- Useful for historical data

### 3. Contract Period Calculation

The system calculates contract end date based on:
```
con_start = 2025-10-15
con_period = 6 (months)
con_end = 2026-04-14 (approximately 6 months from start)
```

---

## Database Relationships

```
┌─────────────────┐      ┌──────────────────┐      ┌─────────────────┐
│   contractors   │      │ contract_worker  │      │     workers     │
│                 │      │                  │      │                 │
│ ctr_clab_no (PK)│◄─────│ con_ctr_clab_no  │      │ wkr_id (PK)     │
│ ctr_comp_name   │      │ con_wkr_id       │─────►│ wkr_name        │
│ ...             │      │ con_period       │      │ wkr_passno      │
└─────────────────┘      │ con_start        │      │ ...             │
                         │ con_end          │      └─────────────────┘
                         └──────────────────┘
```

**Flow:**
1. Admin adds a contract in `contract_worker` table
2. This links a contractor (CLAB number) to a worker (worker ID)
3. System only processes payroll for workers in `contract_worker`
4. When contract expires (`con_end < today`), worker is automatically excluded

---

## Best Practices

### 1. Always Use Services

❌ **Don't do this:**
```php
$contracts = ContractWorker::where('con_ctr_clab_no', $clabNo)->get();
```

✅ **Do this:**
```php
$service = new ContractWorkerService();
$contracts = $service->getContractsByContractor($clabNo);
```

### 2. Filter by Active Contracts

When showing current workers, always filter by active contracts:
```php
$workers = $service->getActiveContractsByContractor($clabNo);
```

### 3. Invalidate Cache on Changes

When adding/updating/deleting contracts:
```php
// After adding a new contract
$service->invalidateContractCache($contractId);
```

### 4. Use Eager Loading

Load relationships to avoid N+1 queries:
```php
$contracts = ContractWorker::with(['contractor', 'worker'])->get();
```

---

## Common Queries

### Get all workers for payroll processing

```php
$service = new ContractWorkerService();
$stats = $service->getContractStatistics();
$activeWorkerCount = $stats['active_workers'];

// Get contracted workers by contractor
foreach ($contractors as $contractor) {
    $workers = $service->getContractedWorkers($contractor->ctr_clab_no);
    // Process payroll for these workers
}
```

### Check contract status

```php
$contract = ContractWorker::find($id);

if ($contract->isActive()) {
    echo "Contract is active - {$contract->daysRemaining()} days remaining";
} else {
    echo "Contract expired";
}
```

### Find workers without active contracts

```php
// Workers in worker_db but not in active contracts
$allWorkerIds = Worker::pluck('wkr_id');
$contractedWorkerIds = ContractWorker::active()->pluck('con_wkr_id');

$uncontractedWorkerIds = $allWorkerIds->diff($contractedWorkerIds);
```

---

## Testing

### View Contract Data

```bash
php artisan db:view-contract-worker
```

### Test in Tinker

```bash
php artisan tinker
```

```php
// Get a contract
$contract = App\Models\ContractWorker::first();
echo $contract->contractor->company_name;
echo $contract->worker->name;
echo $contract->isActive();

// Use service
$service = new App\Services\ContractWorkerService();
$stats = $service->getContractStatistics();
print_r($stats);
```

---

## Summary

✅ **contract_worker table** - Junction table linking contractors to workers
✅ **ContractWorker model** - Eloquent model with relationships
✅ **ContractWorkerService** - Service layer with caching
✅ **Updated Contractor & Worker models** - Include contract relationships
✅ **Cached access** - 1-hour TTL by default

**Key Concept:** The payroll system only processes workers that exist in the `contract_worker` table with active contracts (`con_end >= today`). This filters the 69,726 workers down to only those actively contracted.
