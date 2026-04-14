<?php

namespace App\Providers;

use App\Models\PurchaseLine;
use App\Observers\PurchaseLineObserver;
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
        PurchaseLine::observe(PurchaseLineObserver::class);
    }
}
