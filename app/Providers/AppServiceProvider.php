<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\TrackingLog;
use App\Observers\TrackingLogObserver;
use App\Services\GoogleSyncService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GoogleSyncService::class);
    }

    public function boot(): void
    {
        TrackingLog::observe(TrackingLogObserver::class);
    }
}
