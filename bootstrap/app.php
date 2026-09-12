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

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('sampling/*') || $request->is('sampling')) {
                return route('sampling.login');
            }
            if ($request->is('supplier/*') || $request->is('supplier')) {
                return route('supplier.login');
            }
            return route('login');
        });

        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            if (\Illuminate\Support\Facades\Auth::guard('sampling')->check()) {
                return route('sampling.dashboard');
            }
            if (\Illuminate\Support\Facades\Auth::guard('supplier')->check()) {
                return route('supplier.dashboard');
            }
            return '/';
        });

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