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
    Route::view('dashboard', 'client.dashboard')->name('dashboard');
    Route::view('workers', 'client.workers')->name('workers');
    Route::view('payments', 'client.payments')->name('payments');
    Route::view('invoices', 'client.invoices')->name('invoices');
    Route::view('timesheet', 'client.timesheet')->name('timesheet');
});

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
