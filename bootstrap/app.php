<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'prevent.back' => \App\Http\Middleware\PreventBackHistory::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'api/*',
            'webhook/*',
            'webhooks/*',
            'order_webhook_payloads',
            'order_webhook_payloads/*',
            'orders/webhook',
            'order-webhook',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();