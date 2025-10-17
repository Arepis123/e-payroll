<?php

namespace App\Providers;

use App\Auth\ThirdPartyUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register custom authentication provider for third-party database
        Auth::provider('third_party', function ($app, array $config) {
            return new ThirdPartyUserProvider();
        });
    }
}
