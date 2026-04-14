<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureInitialSetupCompleted;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'initial.setup' => EnsureInitialSetupCompleted::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('licoreria:daily-alerts')->dailyAt('22:00');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
