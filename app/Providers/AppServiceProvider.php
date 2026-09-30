<?php

namespace App\Providers;

use App\Models\Sensor;
use App\Observers\SensorObserver;
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
        Sensor::observe(SensorObserver::class);
    }
}
