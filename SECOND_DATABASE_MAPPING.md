# Second Database Field Mapping

This document shows how the live database columns map to the Laravel models.

## Database Overview

- **Total Contractors:** 5,809
- **Total Workers:** 69,726
- **Connection Name:** `worker_db`

---

## Contractor Model Mapping

### Database Table: `contractors`
**Primary Key:** `ctr_clab_no` (string, e.g., "CLAB000001")

| Model Accessor       | Database Column        | Description                    | Example                      |
|---------------------|------------------------|--------------------------------|------------------------------|
| `company_name`      | `ctr_comp_name`        | Company name                   | NS CONSTRUCTION SDN. BHD.    |
| `registration_number`| `ctr_comp_regno`      | Company registration number    | 16780-P                      |
| `contact_person`    | `ctr_contact_name`     | Contact person name            | ANUAR B ABU BAKAR            |
| `phone`             | `ctr_contact_mobileno` | Mobile number (or ctr_telno)   | 60122412702                  |
| `email`             | `ctr_email`            | Email address                  | construction@kemasco.com     |
| `status`            | `ctr_status`           | Status (2=active, 1=inactive)  | active/inactive              |

### Key Columns:
- `ctr_clab_no` - CLAB registration number (Primary Key)
- `ctr_comp_name` - Company name
- `ctr_comp_regno` - Company registration number
- `ctr_cidb_regno` - CIDB registration number
- `ctr_grade` - CIDB grade (G1, G7, etc.)
- `ctr_addr1`, `ctr_addr2`, `ctr_addr3` - Address lines
- `ctr_pcode` - Postal code
- `ctr_state` - State code
- `ctr_telno` - Telephone number
- `ctr_contact_name` - Contact person name
- `ctr_contact_mobileno` - Contact mobile number
- `ctr_email` - Email address
- `ctr_datereg` - Registration date
- `ctr_clabexp_date` - CLAB expiry date
- `ctr_status` - Status (1=inactive, 2=active)

### Example Contractors:
1. **CLAB000001** - NS CONSTRUCTION SDN. BHD.
2. **CLAB000002** - GAMUDA ENGINEERING SDN. BHD.
3. **CLAB000003** - KEMAS CONSTRUCTION SDN. BHD.

---

## Worker Model Mapping

### Database Table: `workers`
**Primary Key:** `wkr_id` (integer)

| Model Accessor       | Database Column      | Description                    | Example           |
|---------------------|----------------------|--------------------------------|-------------------|
| `name`              | `wkr_name`           | Worker name                    | ABDUL ROHIM       |
| `ic_number`         | `wkr_passno`         | Passport number                | AP027471          |
| `position`          | `wkr_wtrade`         | Worker trade/position          | GW (General Worker)|
| `basic_salary`      | `wkr_salary`         | Salary                         | 35.00             |
| `status`            | `wkr_status`         | Status (2=active, 1=inactive)  | active/inactive   |
| `phone`             | `wkr_tel`            | Telephone number               | +6012345678       |
| `contractor_id`     | `wkr_currentemp`     | Current employer CLAB number   | CLAB002439        |

### Key Columns:
- `wkr_id` - Worker ID (Primary Key)
- `wkr_passno` - Passport number
- `wkr_name` - Worker name
- `wkr_dob` - Date of birth
- `wkr_nationality` - Nationality code
- `wkr_gender` - Gender
- `wkr_wtrade` - Worker trade/position
- `wkr_salary` - Salary amount
- `wkr_currentemp` - Current employer (CLAB number)
- `wkr_address1`, `wkr_address2`, `wkr_address3` - Address lines
- `wkr_pcode` - Postal code
- `wkr_state` - State code
- `wkr_tel` - Telephone number
- `wkr_passexp` - Passport expiry date
- `wkr_permitexp` - Permit expiry date
- `wkr_status` - Status (1=inactive, 2=active)

### Example Workers:
1. **ID 24349** - ABDUL ROHIM (AP027471) - GW @ CLAB002439
2. **ID 24274** - MUHAMMAD KHALID (MH4118351) - GW @ CLAB002337
3. **ID 24275** - MUHAMMAD ARIF (BA1981781) - GW @ CLAB002337

---

## Relationships

### Contractor → Workers
```php
$contractor = Contractor::find('CLAB000001');
$workers = $contractor->workers; // HasMany relationship
```

**Foreign Key Mapping:**
- Contractor: `ctr_clab_no` (Primary Key)
- Worker: `wkr_currentemp` (Foreign Key)

### Worker → Contractor
```php
$worker = Worker::find(24349);
$contractor = $worker->contractor; // BelongsTo relationship
```

---

## Status Codes

### Contractor Status (`ctr_status`)
- `1` = Inactive
- `2` = Active
- `3` = Other status (check database for meaning)

### Worker Status (`wkr_status`)
- `1` = Inactive
- `2` = Active
- `3` = Other status (check database for meaning)

---

## Common Queries

### Get Active Contractors
```php
$contractors = Contractor::active()->get();
// Queries: WHERE ctr_status = '2'
```

### Get Active Workers
```php
$workers = Worker::active()->get();
// Queries: WHERE wkr_status = '2'
```

### Get Workers by Contractor
```php
$workers = Worker::where('wkr_currentemp', 'CLAB000001')->get();
```

### Search Workers by Name
```php
$workers = Worker::where('wkr_name', 'LIKE', '%ABDUL%')->get();
```

### Search Contractors by Company Name
```php
$contractors = Contractor::where('ctr_comp_name', 'LIKE', '%CONSTRUCTION%')->get();
```

---

## Using Services (Recommended)

Always use the services for cached access:

### WorkerService
```php
use App\Services\WorkerService;

$workerService = new WorkerService();

// Get single worker (cached)
$worker = $workerService->getWorker(24349);

// Get workers by contractor (cached)
$workers = $workerService->getWorkersByContractor('CLAB000001');

// Search workers (cached)
$workers = $workerService->searchWorkers('ABDUL');

// Get active workers (cached)
$workers = $workerService->getActiveWorkers();
```

### ContractorService
```php
use App\Services\ContractorService;

$contractorService = new ContractorService();

// Get contractor (cached)
$contractor = $contractorService->getContractor('CLAB000001');

// Get contractor with workers (cached)
$contractor = $contractorService->getContractorWithWorkers('CLAB000001');

// Search contractors (cached)
$contractors = $contractorService->searchContractors('GAMUDA');

// Get active contractors (cached)
$contractors = $contractorService->getActiveContractors();
```

---

## Accessors Added

The models include accessors to maintain consistent naming:

### Contractor Accessors
- `$contractor->company_name` → reads `ctr_comp_name`
- `$contractor->registration_number` → reads `ctr_comp_regno`
- `$contractor->contact_person` → reads `ctr_contact_name`
- `$contractor->phone` → reads `ctr_contact_mobileno` or `ctr_telno`
- `$contractor->email` → reads `ctr_email`
- `$contractor->status` → converts `ctr_status` to 'active'/'inactive'

### Worker Accessors
- `$worker->name` → reads `wkr_name`
- `$worker->ic_number` → reads `wkr_passno`
- `$worker->position` → reads `wkr_wtrade`
- `$worker->basic_salary` → reads `wkr_salary`
- `$worker->status` → converts `wkr_status` to 'active'/'inactive'
- `$worker->phone` → reads `wkr_tel`
- `$worker->contractor_id` → reads `wkr_currentemp`

---

## Testing Commands

### View Database Structure
```bash
php artisan db:structure
```

### View Sample Data
```bash
php artisan db:view-workers
```

### Test Connection
```bash
php artisan db:test-worker
```

---

## Important Notes

1. **Primary Keys:**
   - Contractor uses string primary key: `ctr_clab_no`
   - Worker uses integer primary key: `wkr_id`

2. **Foreign Key Relationship:**
   - Workers link to contractors via `wkr_currentemp` → `ctr_clab_no`

3. **Status Values:**
   - Active = '2' (not '1' or 'active' string)
   - Inactive = '1'

4. **Cache Strategy:**
   - All service methods use 1-hour cache by default
   - Always use services instead of direct model access
   - This prevents N+1 queries and reduces load on second database

5. **Large Dataset:**
   - 69,726 workers - always use pagination or limits
   - 5,809 contractors
   - Never use `::all()` without limits in production

---

## Next Steps

1. ✅ Models updated with correct field mapping
2. ✅ Services updated with correct field names
3. ✅ Accessors added for consistent naming
4. ✅ Test commands working

**Ready to use in your application!**

To integrate into your existing code, update any controllers or views to use the WorkerService and ContractorService instead of direct model queries.
