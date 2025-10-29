<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

// Super Admin Routes (Only for super_admin role)
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('configuration', \App\Livewire\Admin\Configuration::class)->name('configuration');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('worker', \App\Livewire\Admin\Worker::class)->name('worker');
    Route::get('workers/{worker}', [\App\Http\Controllers\Admin\WorkerController::class, 'show'])->name('workers.show');
    Route::get('salary', \App\Livewire\Admin\Salary::class)->name('salary');
    Route::get('salary/{id}', \App\Livewire\Admin\SalaryDetail::class)->name('salary.detail');
    Route::get('missing-submissions', \App\Livewire\Admin\MissingSubmissions::class)->name('missing-submissions');
    Route::get('missing-submissions/{clabNo}', \App\Livewire\Admin\MissingSubmissionsDetail::class)->name('missing-submissions.detail');
    Route::get('invoices', \App\Livewire\Admin\Invoices::class)->name('invoices');
    Route::get('invoices/{id}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{id}/download', [\App\Http\Controllers\Admin\InvoiceController::class, 'download'])->name('invoices.download');
    Route::get('notifications', \App\Livewire\Admin\Notifications::class)->name('notifications');
    Route::get('report', \App\Livewire\Admin\Report::class)->name('report');
    Route::get('news', \App\Livewire\Admin\NewsManagement::class)->name('news');
});

// Client/Contractor Routes
Route::middleware(['auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
    Route::get('workers', \App\Livewire\Client\Workers::class)->name('workers');
    Route::get('workers/{worker}', [\App\Http\Controllers\Client\WorkersController::class, 'show'])->name('workers.show');
    Route::get('timesheet', \App\Livewire\Client\Timesheet::class)->name('timesheet');
    Route::get('timesheet/{id}', [\App\Http\Controllers\Client\TimesheetController::class, 'show'])->name('timesheet.show');
    Route::get('timesheet/{id}/edit', \App\Livewire\Client\TimesheetEdit::class)->name('timesheet.edit');

    // Payment routes
    Route::post('payment/{submission}', [\App\Http\Controllers\Client\PaymentController::class, 'createPayment'])->name('payment.create');

    Route::get('payments', \App\Livewire\Client\Payments::class)->name('payments');
    Route::get('invoices', \App\Livewire\Client\Invoices::class)->name('invoices');
    Route::get('invoices/{id}', [\App\Http\Controllers\Client\InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{id}/download', [\App\Http\Controllers\Client\InvoiceController::class, 'download'])->name('invoices.download');
});

// Billplz routes (No auth/middleware required - users may have lost session when redirected back from payment gateway)
Route::post('/billplz/callback', [\App\Http\Controllers\Client\PaymentController::class, 'callback'])->name('billplz.callback');
Route::get('/client/payment/{submission}/return', [\App\Http\Controllers\Client\PaymentController::class, 'return'])->name('client.payment.return');

// Legacy routes (for backward compatibility - redirect based on role)
Route::get('dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('client.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::view('posts', 'posts')
    ->middleware(['auth', 'verified'])
    ->name('posts');   

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
