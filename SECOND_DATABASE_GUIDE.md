# Second Database Integration Guide

## Overview

This system uses **Strategy 1: Read-Through Cache** to efficiently access worker and contractor data from a second database while keeping the primary database lightweight.

## Architecture

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│  First Database │────▶│  Cache Layer     │◀────│ Second Database │
│  (e_salary)     │     │  (Database/Redis)│     │ (worker_db)     │
└─────────────────┘     └──────────────────┘     └─────────────────┘
```

### What's Stored Where:

**First Database (e_salary):**
- User accounts and roles
- Client-worker relationships
- Payroll records
- Timesheet submissions
- Payment history

**Second Database (worker_db):**
- Worker master data
- Contractor master data
- Worker-Contractor relationships

**Cache:**
- Frequently accessed worker/contractor data
- 1-hour TTL by default
- Automatic invalidation on updates

---

## Configuration

### 1. Environment Variables (.env)

```env
# First Database
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3310
DB_DATABASE=e_salary
DB_USERNAME=root
DB_PASSWORD=

# Second Database (Worker Database)
WORKER_DB_HOST=127.0.0.1
WORKER_DB_PORT=3310
WORKER_DB_DATABASE=worker_db
WORKER_DB_USERNAME=root
WORKER_DB_PASSWORD=
```

### 2. Database Configuration

Already configured in `config/database.php` with connection name `worker_db`.

### 3. Cache Configuration

Current: Database cache (`CACHE_STORE=database`)
Recommended: Redis for better performance

To use Redis:
```env
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

## Models

### Worker Model

```php
use App\Models\Worker;

// Get worker (from second DB)
$worker = Worker::find(1);

// Get active workers
$workers = Worker::active()->get();

// Get workers by position
$carpenters = Worker::position('Carpenter')->get();

// With contractor relationship
$worker = Worker::with('contractor')->find(1);
echo $worker->contractor->company_name;
```

**Model Features:**
- ✅ Points to `worker_db` connection
- ✅ Active scope for status filtering
- ✅ Position scope
- ✅ Contractor relationship (belongsTo)
- ✅ Cache key helper method

### Contractor Model

```php
use App\Models\Contractor;

// Get contractor (from second DB)
$contractor = Contractor::find(1);

// Get active contractors
$contractors = Contractor::active()->get();

// With workers relationship
$contractor = Contractor::with('workers')->find(1);
foreach ($contractor->workers as $worker) {
    echo $worker->name;
}
```

**Model Features:**
- ✅ Points to `worker_db` connection
- ✅ Active scope for status filtering
- ✅ Workers relationship (hasMany)
- ✅ Cache key helper method

---

## Services with Caching

### WorkerService

Provides cached access to worker data from the second database.

#### Basic Usage

```php
use App\Services\WorkerService;

$workerService = new WorkerService();

// Get single worker (cached for 1 hour)
$worker = $workerService->getWorker(1);

// Get multiple workers
$workers = $workerService->getWorkers([1, 2, 3]);

// Get active workers
$activeWorkers = $workerService->getActiveWorkers();

// Search workers
$results = $workerService->searchWorkers('John');

// Get workers by contractor
$contractorWorkers = $workerService->getWorkersByContractor(5);

// Get workers by position
$carpenters = $workerService->getWorkersByPosition('Carpenter');

// Get statistics
$stats = $workerService->getWorkerStatistics();
// Returns: ['total' => 342, 'active' => 318, 'by_position' => [...]]
```

#### Cache Management

```php
// Invalidate specific worker cache
$workerService->invalidateWorkerCache(1);

// Invalidate all worker caches
$workerService->invalidateAllWorkerCaches();

// Warm up cache for frequently accessed workers
$workerService->warmCache([1, 2, 3, 4, 5]);

// Set custom cache TTL (30 minutes)
$workerService->setCacheTTL(1800)->getWorker(1);
```

### ContractorService

Provides cached access to contractor data from the second database.

#### Basic Usage

```php
use App\Services\ContractorService;

$contractorService = new ContractorService();

// Get single contractor (cached)
$contractor = $contractorService->getContractor(1);

// Get contractor with workers
$contractor = $contractorService->getContractorWithWorkers(1);

// Get active contractors
$contractors = $contractorService->getActiveContractors();

// Search contractors
$results = $contractorService->searchContractors('Miqabina');

// Get contractors with active contracts
$active = $contractorService->getContractorsWithActiveContracts();

// Get statistics
$stats = $contractorService->getContractorStatistics();
// Returns: ['total' => 24, 'active' => 20, 'total_workers' => 342]
```

#### Cache Management

```php
// Invalidate specific contractor cache
$contractorService->invalidateContractorCache(1);

// Invalidate all contractor caches
$contractorService->invalidateAllContractorCaches();

// Set custom cache TTL
$contractorService->setCacheTTL(7200)->getContractor(1);
```

---

## Usage in Controllers

### Example: Worker Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\WorkerService;
use Illuminate\Http\Request;

class WorkerController extends Controller
{
    protected WorkerService $workerService;

    public function __construct(WorkerService $workerService)
    {
        $this->workerService = $workerService;
    }

    public function index()
    {
        $workers = $this->workerService->getActiveWorkers();
        return view('admin.workers.index', compact('workers'));
    }

    public function show($id)
    {
        $worker = $this->workerService->getWorker($id);

        if (!$worker) {
            abort(404);
        }

        return view('admin.workers.show', compact('worker'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $workers = $this->workerService->searchWorkers($query);

        return response()->json($workers);
    }
}
```

---

## Usage in Livewire Components

### Example: Worker List Component

```php
<?php

namespace App\Livewire;

use App\Services\WorkerService;
use Livewire\Component;

class WorkerList extends Component
{
    public $search = '';
    public $contractorId = null;

    public function render(WorkerService $workerService)
    {
        $workers = $this->search
            ? $workerService->searchWorkers($this->search)
            : ($this->contractorId
                ? $workerService->getWorkersByContractor($this->contractorId)
                : $workerService->getActiveWorkers());

        return view('livewire.worker-list', [
            'workers' => $workers,
        ]);
    }
}
```

---

## Cache Invalidation Strategies

### When Worker Data Changes

If workers are updated in the second database, you need to invalidate the cache:

```php
use App\Services\WorkerService;

$workerService = new WorkerService();

// After updating a worker
$workerService->invalidateWorkerCache($workerId);

// After bulk updates
$workerService->invalidateAllWorkerCaches();
```

### Event-Based Invalidation (Advanced)

If you have control over the second database updates:

```php
// Listen for worker updates
Event::listen('worker.updated', function ($workerId) {
    $workerService = app(WorkerService::class);
    $workerService->invalidateWorkerCache($workerId);
});
```

### Scheduled Cache Refresh

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Refresh worker cache every hour
    $schedule->call(function () {
        app(WorkerService::class)->invalidateAllWorkerCaches();
    })->hourly();
}
```

---

## Testing the Setup

### Test Database Connection

```bash
php artisan db:test-worker
```

Expected output:
```
Testing worker database connection...

📊 Testing First Database (e_salary):
   ✓ Connected to: e_salary
   ✓ Tables found: 10

👷 Testing Second Database (worker_db):
   ✓ Connected to: worker_db
   ✓ Tables found: X

✅ Both database connections are working!
```

### Test in Tinker

```bash
php artisan tinker
```

```php
// Test Worker Model
$worker = App\Models\Worker::first();
echo $worker->name;

// Test Contractor Model
$contractor = App\Models\Contractor::first();
echo $contractor->company_name;

// Test WorkerService
$service = new App\Services\WorkerService();
$workers = $service->getActiveWorkers();
echo count($workers);

// Test ContractorService
$service = new App\Services\ContractorService();
$contractors = $service->getActiveContractors();
echo count($contractors);
```

---

## Performance Tips

### 1. Use Eager Loading

```php
// ❌ Bad: N+1 queries
foreach ($workers as $worker) {
    echo $worker->contractor->company_name;
}

// ✅ Good: Single query
$workers = Worker::with('contractor')->get();
foreach ($workers as $worker) {
    echo $worker->contractor->company_name;
}
```

### 2. Cache Warm-Up

```php
// Warm cache on application startup for frequently accessed data
$workerService = new WorkerService();
$workerService->warmCache([1, 2, 3, 4, 5]);
```

### 3. Selective Caching

```php
// Long cache for rarely changing data
$workerService->setCacheTTL(86400)->getWorker($id); // 24 hours

// Short cache for frequently changing data
$workerService->setCacheTTL(300)->getActiveWorkers(); // 5 minutes
```

### 4. Use Redis for Better Performance

Redis is significantly faster than database caching:
- Database cache: ~10-50ms
- Redis cache: <1ms

---

## Troubleshooting

### Connection Issues

If you get connection errors:

1. **Check .env configuration**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Verify database exists**
   ```bash
   php artisan db:test-worker
   ```

3. **Check MySQL/MariaDB is running**
   - Open XAMPP Control Panel
   - Ensure MySQL is started

### Cache Not Working

1. **Clear cache**
   ```bash
   php artisan cache:clear
   ```

2. **Check cache driver**
   ```bash
   php artisan config:show cache.default
   ```

### Performance Issues

1. **Switch to Redis**
   ```env
   CACHE_STORE=redis
   ```

2. **Adjust cache TTL**
   ```php
   $workerService->setCacheTTL(7200); // 2 hours
   ```

3. **Monitor cache hit rate**
   - Check logs or use Laravel Telescope

---

## Summary

✅ **Second database configured** (`worker_db`)
✅ **Models created** (Worker, Contractor)
✅ **Services with caching** (WorkerService, ContractorService)
✅ **Cache management** (invalidation, warm-up)
✅ **Test command** (`php artisan db:test-worker`)

**Next Steps:**
1. Update `.env` with correct second database credentials
2. Test connection with `php artisan db:test-worker`
3. Use services in your controllers/components
4. Consider upgrading to Redis for better cache performance
