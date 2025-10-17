# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an e-payroll system built with Laravel 12, Livewire 3, and Livewire Flux/Flux Pro UI components. The application handles employee salary management with features for workers, salary processing, and reporting.

## Development Environment

### XAMPP Setup
This project runs in a XAMPP environment on Windows:
- MariaDB runs on **port 3310** (not the default 3306)
- Database name: `e_salary`
- PHP artisan serve typically conflicts with other XAMPP apps on port 8000, use alternate ports like 9000

### Starting Development Servers

**Start all services concurrently (recommended):**
```bash
composer dev
```
This runs `php artisan serve`, `php artisan queue:listen`, and `npm run dev` concurrently.

**Start services individually:**
```bash
php artisan serve --port=9000    # Start Laravel dev server (use alternate port to avoid conflicts)
npm run dev                       # Start Vite dev server for assets
php artisan queue:listen --tries=1  # Process queue jobs
```

### Common Port Conflicts
If you see another application when accessing localhost:
1. Check for existing processes: `netstat -ano | findstr :8000`
2. Kill conflicting processes: `taskkill //PID <pid> //F`
3. Start Laravel on a different port: `php artisan serve --port=9000`

## Architecture

### Frontend Stack
- **Livewire 3**: Full-page components with real-time reactivity
- **Livewire Volt**: Single-file Livewire components
- **Flux/Flux Pro**: Premium UI component library for Livewire
- **Tailwind CSS 4**: Utility-first CSS framework with Vite plugin
- **Vite**: Frontend build tool with HMR

### Backend Stack
- **Laravel 12** (PHP 8.2+)
- **MariaDB**: Primary database
- **Queue System**: Database-backed queue for async jobs
- **Cache**: Database-backed cache
- **Session**: Database-backed sessions

### Key Application Areas
The application is divided into main functional areas (routes/web.php):
- **Dashboard**: Main authenticated landing page
- **Worker**: Employee management
- **Salary**: Payroll processing
- **Report**: Reporting and analytics
- **Posts**: Content management (example feature)
- **Settings**: User profile, password, and appearance preferences

### Livewire Architecture
All interactive pages use Livewire components located in `app/Livewire/`:
- **Auth components**: Login, Register, Password Reset, Email Verification
- **Settings components**: Profile, Password, Appearance, DeleteUserForm
- **Feature components**: Posts, PostCreate, PostEdit
- Views are in `resources/views/livewire/`

### Authentication & Authorization
- Uses Laravel's built-in authentication with Livewire components
- Routes are protected with `auth` and `verified` middleware
- Email verification is enforced on protected routes
- Settings area accessible only to authenticated users

## Database

### Running Migrations
```bash
php artisan migrate
```

### Database Configuration Notes
- Connection type: `mariadb`
- Default port in `.env`: 3310 (non-standard, specific to this XAMPP setup)
- Must start MySQL/MariaDB in XAMPP Control Panel before running migrations
- Fresh database requires running migrations to create required tables

### Key Tables
- `users`: User accounts with email verification
- `sessions`: Database-backed sessions
- `cache`: Database cache storage
- `jobs`, `job_batches`, `failed_jobs`: Queue system tables
- `posts`: Example content management table

## Testing

**Run tests:**
```bash
composer test
# or directly:
php artisan test
```
Tests use PestPHP with Laravel plugin.

## Code Formatting

**Run Laravel Pint (code style fixer):**
```bash
./vendor/bin/pint
```

## Deployment & Build

**Build for production:**
```bash
npm run build
```

**Optimize Laravel:**
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Livewire Flux Notes

This project uses both Flux and Flux Pro (premium UI components). The Flux Pro repository requires authentication via `composer.fluxui.dev`. Custom Flux icons and components are in `resources/views/flux/`.

## Salary & Payment Calculation

This system is designed for managing foreign construction worker payroll in Malaysia, following official regulations and formulas.

### Payment Formula Reference
The official payment calculation formulas are documented in: `public/FORMULA PENGIRAAN GAJI DAN OVERTIME.csv`

### Payment Calculation Service
Use `App\Services\PaymentCalculatorService` for all salary and overtime calculations. This service implements the official Malaysian labor regulations.

### Salary Components (Based on RM 1,700 minimum)

**Worker Deductions:**
- EPF/KWSP (Worker): 2% = RM 34.00
- PERKESO/SOCSO (Worker): 0.5% = RM 8.50
- **Total Worker Deductions: RM 42.50**

**Employer Contributions:**
- EPF/KWSP (Employer): 2% = RM 34.00
- PERKESO/SOCSO (Employer): 1.75% = RM 29.75
- **Total Employer Contributions: RM 63.75**

**Payment Breakdown:**
- **Gaji Pokok (Basic Salary)**: RM 1,700.00
- **Gaji Bersih (Net Salary)**: RM 1,700.00 - RM 42.50 = **RM 1,657.50** (what worker receives)
- **Total Payment to CLAB**: RM 1,700.00 + RM 63.75 = **RM 1,763.75** (what system collects)

### Overtime Calculation Formulas

**Rate Calculation:**
- **Daily Rate (ORP)**: Basic Salary ÷ 26 working days = RM 65.38
- **Hourly Rate (HRP)**: Daily Rate ÷ 8 hours = RM 8.17

**Overtime Multipliers:**
- **Hari Biasa (Weekday OT)**: Hourly Rate × 1.5 = RM 12.26/hour
- **Cuti Rehat (Rest Day OT)**: Hourly Rate × 2.0 = RM 16.34/hour
- **Cuti Umum (Public Holiday OT)**: Hourly Rate × 3.0 = RM 24.51/hour

### Using PaymentCalculatorService

```php
use App\Services\PaymentCalculatorService;

$calculator = new PaymentCalculatorService();

// Calculate complete worker payment
$payment = $calculator->calculateWorkerPayment(
    basicSalary: 1700,
    weekdayOTHours: 10,
    restDayOTHours: 8,
    publicHolidayOTHours: 0
);

// Get formatted summary
$summary = $calculator->getPaymentSummary($payment);

// Access specific calculations
$netSalary = $calculator->calculateNetSalary(1700);
$totalToCLAB = $calculator->calculateTotalPaymentToCLAB(1700);
$overtimeRate = $calculator->calculateWeekdayOTRate(1700);
```

### PayrollWorker Model
The `PayrollWorker` model has a `calculateSalary()` method that automatically calculates all salary components:

```php
$worker = new PayrollWorker([
    'basic_salary' => 1700,
    'ot_normal_hours' => 10,
    'ot_rest_hours' => 8,
    'ot_public_hours' => 0,
]);

$worker->calculateSalary(); // Calculates all fields automatically
$worker->save();
```

### Important Notes
- Minimum salary for foreign construction workers: **RM 1,700**
- This system collects: **Basic Salary + Employer Contributions**
- The system owner deducts worker EPF/SOCSO from the RM 1,700 and pays the net amount to workers
- All calculations follow Malaysian labor regulations
- Use `PaymentCalculatorService` for consistency across the application

## Important Configuration

- `SESSION_DRIVER=database`: Sessions stored in database, not files
- `QUEUE_CONNECTION=database`: Jobs stored in database queue
- `CACHE_STORE=database`: Cache stored in database
- Vite dev server has CORS enabled for HMR
- Asset inputs: `resources/css/app.css`, `resources/js/app.js`