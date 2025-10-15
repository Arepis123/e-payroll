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

## Important Configuration

- `SESSION_DRIVER=database`: Sessions stored in database, not files
- `QUEUE_CONNECTION=database`: Jobs stored in database queue
- `CACHE_STORE=database`: Cache stored in database
- Vite dev server has CORS enabled for HMR
- Asset inputs: `resources/css/app.css`, `resources/js/app.js`
- Bear in mind, from now on, salary for foreign construction worker in Malaysia will start from RM1,700 and there will be A deduction 2% from salary for EPF/KWSP