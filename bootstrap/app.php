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
    ->withMiddleware(function (Middleware $middleware): void {
        // Cloudflare Tunnel / nginx-এর পেছনে চলে — আসল ভিজিটর IP ও https স্কিম
        // X-Forwarded-* হেডার থেকে নিতে হবে (অ্যানালিটিক্স + Meta CAPI-র জন্য জরুরি)
        $middleware->trustProxies(at: '*');

        // অ্যানালিটিক্স beacon (navigator.sendBeacon) হেডার পাঠাতে পারে না
        $middleware->validateCsrfTokens(except: ['track']);

        $middleware->alias([
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
