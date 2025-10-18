<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('dashboard', 'admin.dashboard')->name('dashboard');
    Route::view('worker', 'admin.worker')->name('worker');
    Route::view('salary', 'admin.salary')->name('salary');
    Route::view('report', 'admin.report')->name('report');
});

// Client/Contractor Routes
Route::middleware(['auth', 'verified', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Client\DashboardController::class, 'index'])->name('dashboard');
    Route::get('workers', [\App\Http\Controllers\Client\WorkersController::class, 'index'])->name('workers');
    Route::get('workers/{worker}', [\App\Http\Controllers\Client\WorkersController::class, 'show'])->name('workers.show');
    Route::get('timesheet', [\App\Http\Controllers\Client\TimesheetController::class, 'index'])->name('timesheet');
    Route::post('timesheet', [\App\Http\Controllers\Client\TimesheetController::class, 'store'])->name('timesheet.store');
    Route::get('timesheet/{id}', [\App\Http\Controllers\Client\TimesheetController::class, 'show'])->name('timesheet.show');
    Route::get('timesheet/{id}/edit', [\App\Http\Controllers\Client\TimesheetController::class, 'edit'])->name('timesheet.edit');
    Route::post('timesheet/{id}/submit', [\App\Http\Controllers\Client\TimesheetController::class, 'submitDraft'])->name('timesheet.submit');

    // Payment routes
    Route::post('payment/{submission}', [\App\Http\Controllers\Client\PaymentController::class, 'createPayment'])->name('payment.create');

    Route::get('payments', [\App\Http\Controllers\Client\PaymentHistoryController::class, 'index'])->name('payments');
    Route::get('invoices', [\App\Http\Controllers\Client\InvoiceController::class, 'index'])->name('invoices');
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
