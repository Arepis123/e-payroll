# Second Database - Read-Only Access Guide

## ⚠️ Important: All Second Database Models are READ-ONLY

The entire second database (`worker_db`) is managed by another system. This payroll system has **READ-ONLY** access.

---

## Read-Only Models

These models connect to the second database and should **NEVER** be modified:

### 1. **Contractor** (`app/Models/Contractor.php`)
- ❌ Do NOT create new contractors
- ❌ Do NOT update contractor information
- ❌ Do NOT delete contractors
- ✅ READ contractor data for display
- ✅ Use in relationships to get contractor info

### 2. **Worker** (`app/Models/Worker.php`)
- ❌ Do NOT create new workers
- ❌ Do NOT update worker information
- ❌ Do NOT delete workers
- ✅ READ worker data for display
- ✅ Use in relationships to get worker info

### 3. **ContractWorker** (`app/Models/ContractWorker.php`)
- ❌ Do NOT create new contracts
- ❌ Do NOT update contract dates
- ❌ Do NOT delete contracts
- ✅ READ contract data to filter active workers
- ✅ Display contract information

---

## What You CAN Do

### ✅ Read Operations (Safe)

```php
// Get contractors
$contractors = Contractor::active()->get();

// Get workers
$workers = Worker::active()->get();

// Get contracts
$contracts = ContractWorker::active()->get();

// Use services (recommended)
$service = new ContractWorkerService();
$contractedWorkers = $service->getContractedWorkers('CLAB005617');

// Display information
echo $contractor->company_name;
echo $worker->name;
echo $contract->con_start;
```

### ✅ Using for Payroll Processing

```php
// Get workers with active contracts for payroll
$service = new ContractWorkerService();
$activeContracts = $service->getActiveContracts();

foreach ($activeContracts as $contract) {
    $worker = $contract->worker;
    $contractor = $contract->contractor;

    // Process payroll based on this data
    $this->calculateSalary($worker, $contractor);

    // Store payroll results in YOUR database (e_salary)
    Payroll::create([
        'worker_id' => $worker->wkr_id,
        'contractor_clab_no' => $contractor->ctr_clab_no,
        'amount' => $calculatedAmount,
        // ... other payroll data
    ]);
}
```

### ✅ Displaying in Views

```blade
@foreach($contractedWorkers as $worker)
    <div>
        <h3>{{ $worker->name }}</h3>
        <p>Passport: {{ $worker->ic_number }}</p>
        <p>Position: {{ $worker->position }}</p>

        @if($worker->contract_info)
            <p>Contract: {{ $worker->contract_info->con_start }} to {{ $worker->contract_info->con_end }}</p>
            <p>Days remaining: {{ $worker->contract_info->daysRemaining() }}</p>
        @endif
    </div>
@endforeach
```

---

## What You CANNOT Do

### ❌ Write Operations (FORBIDDEN)

```php
// ❌ DO NOT DO THESE:

// Creating new records
Contractor::create([...]); // FORBIDDEN
Worker::create([...]); // FORBIDDEN
ContractWorker::create([...]); // FORBIDDEN

// Updating records
$contractor->update([...]); // FORBIDDEN
$worker->save(); // FORBIDDEN
$contract->con_end = now(); // FORBIDDEN
$contract->save(); // FORBIDDEN

// Deleting records
$contractor->delete(); // FORBIDDEN
$worker->delete(); // FORBIDDEN
$contract->delete(); // FORBIDDEN

// Mass updates
Contractor::where(...)->update([...]); // FORBIDDEN
Worker::where(...)->delete(); // FORBIDDEN
```

**Why?** These tables are managed by another system. Any changes you make will be overwritten or cause data inconsistencies.

---

## Data Flow

```
┌─────────────────────────────────────────────────┐
│  Other System (Contract Management)             │
│  - Manages contractors table                    │
│  - Manages workers table                        │
│  - Manages contract_worker table                │
└─────────────────┬───────────────────────────────┘
                  │
                  │ Creates/Updates/Deletes
                  ▼
┌─────────────────────────────────────────────────┐
│  Second Database (worker_db)                    │
│  - contractors (5,809 records)                  │
│  - workers (69,726 records)                     │
│  - contract_worker (3 records)                  │
└─────────────────┬───────────────────────────────┘
                  │
                  │ READ ONLY
                  ▼
┌─────────────────────────────────────────────────┐
│  Your Payroll System                            │
│  - Reads contractor data                        │
│  - Reads worker data                            │
│  - Reads contract data                          │
│  - Processes payroll                            │
│  - Stores payroll results in e_salary database  │
└─────────────────────────────────────────────────┘
```

---

## Your Database (e_salary) - Read/Write

These are the models you CAN create, update, and delete:

### ✅ Writable Models (in e_salary database)

- **User** - User accounts for the payroll system
- **Payroll** - Payroll records (you create these)
- **Timesheet** - Timesheet submissions (you create these)
- **Payment** - Payment records (you create these)
- **Any other models in your e_salary database**

---

## Best Practices

### 1. Always Use Services

Use the provided services to read data - they include caching and proper error handling:

```php
// ✅ Good
$service = new ContractWorkerService();
$workers = $service->getContractedWorkers($clabNo);

// ❌ Avoid direct model access
$workers = Worker::where('wkr_currentemp', $clabNo)->get();
```

### 2. Cache Aggressively

Since you can't modify the data, aggressive caching is safe:

```php
$service = new ContractWorkerService();
$service->setCacheTTL(7200); // 2 hours
$contractors = $service->getContractorsWithActiveContracts();
```

### 3. Store Your Own Data

When you process payroll, store results in YOUR database (e_salary):

```php
// Read from second database
$worker = Worker::find(133951);

// Process payroll
$salary = $this->calculateSalary($worker);

// Store in YOUR database (e_salary)
Payroll::create([
    'worker_id' => $worker->wkr_id,
    'worker_name' => $worker->name, // Denormalize for history
    'amount' => $salary,
    'period' => now()->format('Y-m'),
]);
```

### 4. Denormalize Important Data

Since the second database can change, store important information in your database:

```php
// When creating a payroll record, store worker info
Payroll::create([
    'worker_id' => $worker->wkr_id,
    'worker_name' => $worker->name, // Store name
    'worker_passport' => $worker->ic_number, // Store passport
    'contractor_clab_no' => $contractor->ctr_clab_no,
    'contractor_name' => $contractor->company_name, // Store company name
    'amount' => $salary,
]);
```

This way, even if the worker is deleted from the second database, you still have the historical record.

---

## Handling Changes in Second Database

### What if contracts are added/removed?

The other system manages this. Your payroll system will automatically see the changes:

```php
// This always reflects current active contracts
$service = new ContractWorkerService();
$activeContracts = $service->getActiveContracts();

// If the other system adds a new contract, it will appear here
// If a contract expires, it will disappear here
```

### What if worker data changes?

Worker data in the second database may be updated by the other system. Your payroll system will see the updated data on next read (after cache expires).

**Solution:** Denormalize important data in your payroll records:

```php
// Store worker data at the time of payroll processing
PayrollRecord::create([
    'worker_id' => $worker->wkr_id,
    'worker_name_snapshot' => $worker->name, // Snapshot at time of payroll
    'worker_passport_snapshot' => $worker->ic_number,
    'processed_at' => now(),
]);
```

---

## Troubleshooting

### "I need to add a contract"

❌ You cannot add contracts in this system.
✅ Use the other contract management system to add contracts.
✅ This payroll system will automatically see new contracts after cache refresh.

### "I need to update worker information"

❌ You cannot update worker data in this system.
✅ Use the other system to update worker information.
✅ This payroll system will see updates after cache refresh.

### "Contract data is stale"

Cache might be holding old data. Clear cache:

```bash
php artisan cache:clear
```

Or in code:

```php
$service = new ContractWorkerService();
$service->invalidateAllContractCaches();
```

---

## Summary

| Operation | Contractor | Worker | ContractWorker |
|-----------|-----------|--------|----------------|
| Read      | ✅        | ✅     | ✅             |
| Create    | ❌        | ❌     | ❌             |
| Update    | ❌        | ❌     | ❌             |
| Delete    | ❌        | ❌     | ❌             |

**Remember:** This is a **read-only** integration. Your payroll system reads data from the second database but never modifies it. All payroll-specific data should be stored in your own database (e_salary).
