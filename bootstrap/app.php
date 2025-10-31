<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Global middleware - applies to all requests
        $middleware->web(append: [
            \App\Http\Middleware\ForceHttps::class,
        ]);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Exclude logout from CSRF verification to prevent 419 errors on session expiration
        $middleware->validateCsrfTokens(except: [
            'logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
